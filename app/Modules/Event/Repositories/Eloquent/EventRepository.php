<?php

namespace App\Modules\Event\Repositories\Eloquent;

use App\Modules\Customer\Models\Customer;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;

class EventRepository extends BaseRepository implements EventContract
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    // public function storeModel(array $data): Event
    // {
    //     return DB::transaction(function () use ($data) {

    //         $items = $data['items'] ?? [];
    //         $services = $data['services'] ?? [];

    //         unset($data['items'], $data['services']);

    //         $event = $this->model->create($data);

    //         // Save inventory items
    //         foreach ($items as $item) {
    //             $event->items()->create([
    //                 'inventory_category_id' => $item['inventory_category_id'],
    //                 'inventory_item_id' => $item['inventory_item_id'],
    //                 'quantity' => $item['quantity'],
    //                 'unit_price' => $item['unit_price'],
    //                 'subtotal' => $item['quantity'] * $item['unit_price'],
    //             ]);
    //         }

    //         // Save services
    //         foreach ($services as $service) {
    //             $event->services()->create([
    //                 'service_id' => $service['service_id'],
    //                 'quantity' => $service['quantity'],
    //                 'unit_price' => $service['unit_price'],
    //                 'subtotal' => $service['quantity'] * $service['unit_price'],
    //             ]);
    //         }

    //         return $event;
    //     });
    // }

    public function storeModel(array $payload): Model
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Create Customer
        |--------------------------------------------------------------------------
        */

        $customer = new Customer();
        $customer->toFill($payload['customer']);
        $customer->save();

        /*
        |--------------------------------------------------------------------------
        | 2. Create Event
        |--------------------------------------------------------------------------
        */

        $event = $this->model;
        $event = $payload['event'] ?? [];
        $event['customer_id'] = $customer->id;

        $event->fill($event);
        $event->save();

        /*
        |--------------------------------------------------------------------------
        | 3. Calculate Order Items and Total
        |--------------------------------------------------------------------------
        */

        $lines = [];
        $subtotal = 0;

        foreach ($payload['items'] ?? [] as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $lineTotal = $quantity * $unitPrice;

            $lines[] = [
                'inventory_item_id' => $item['inventory_item_id'],
                'service_id'        => null,
                'quantity'          => $quantity,
                'unit_price'        => $unitPrice,
                'subtotal'          => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        foreach ($payload['services'] ?? [] as $service) {
            $quantity = (float) $service['quantity'];
            $unitPrice = (float) $service['unit_price'];
            $lineTotal = $quantity * $unitPrice;

            $lines[] = [
                'inventory_item_id' => null,
                'service_id'        => $service['service_id'],
                'quantity'          => $quantity,
                'unit_price'        => $unitPrice,
                'subtotal'          => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Create Order
        |--------------------------------------------------------------------------
        */

        $order = new Order();

        $order = Order::create([
            // 'customer_id' => $customer->id,
            'event_id'    => $event->id,
            // 'package_id'  => $payload['package_id'] ?? null,
            'subtotal'    => $subtotal,
            'total'    => $payload['payment']['discount'] ?? 0,
        ]);

        foreach ($lines as $line) {
            $line['order_id'] = $order->id;

            OrderItem::create($line);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Calculate Invoice
        |--------------------------------------------------------------------------
        */

        $discount = (float) ($payload['discount'] ?? 0);
        $securityDeposit = (float) ($payload['security_deposit'] ?? 0);
        $advanceAmount = (float) ($payload['advance_amount'] ?? 0);

        // Assumes discount is a fixed monetary amount.
        $discount = min($discount, $subtotal);

        $total = $subtotal - $discount;

        /*
        |--------------------------------------------------------------------------
        | 6. Create Invoice
        |--------------------------------------------------------------------------
        */

        $invoice = Invoice::create([
            'customer_id'      => $customer->id,
            'event_id'         => $event->id,
            'order_id'         => $order->id,
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'total'            => $total,
            'security_deposit' => $securityDeposit,
            'advance_amount'   => $advanceAmount,
            'balance'          => max(0, $total - $advanceAmount),
        ]);

        foreach ($lines as $line) {
            $line['invoice_id'] = $invoice->id;

            InvoiceItem::create($line);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Record Payment
        |--------------------------------------------------------------------------
        */

        if ($advanceAmount > 0) {
            Payment::create([
                'customer_id'       => $customer->id,
                'event_id'          => $event->id,
                'order_id'          => $order->id,
                'invoice_id'        => $invoice->id,
                'payment_method_id' => $payload['payment_method_id'],
                'amount'            => $advanceAmount,
                'note'              => $payload['note'] ?? null,
            ]);
        }

        return $model;
    }

    // public function updateModel(Model $model, array $payload): Model
    // {
    //     $model->toFill($payload, ['status_id', 'assignee_id']);
    //     $model->save();

    //     $logStatus['amount'] = $payload['budget'];
    //     $logStatus['status_id'] = $payload['status_id'];
    //     $logStatus['assignee_id'] = $payload['assignee_id'];
    //     $logStatus['model_id'] = $model->id;
    //     $logStatus['model_type'] = $model->getMorphClass();

    //     $log = $model->statusLogs()->firstOrNew();
    //     $log->toFill($logStatus);
    //     $log->save();

    //     if(empty($payload['assignee_id'])){
    //         $payload['assignee_id'] = LeadAssigner::getNextAgent($payload['iso_code']); //assignee rol-robbin agent id
    //     }

    //     if (!empty($payload['assignee_id'])) {
    //         $model->assignees()->sync([$payload['assignee_id']]);

    //         // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
    //         $assignees = $model->assignees; // belongsTo
    //         if ($assignees && $assignees->count()) {
    //             $this->sendNotification(
    //                 $model,
    //                 $model->assignees,
    //                 ucfirst(auth()->user()->name) . ' has assigned you a lead',
    //                 "{$model->name} ({$model->email}) - {$model->pipeline}",
    //                 'lead_assigned'
    //             );
    //         }
    //     }else{
    //         $model->assignees()->sync([auth()->id()]);
    //     }

    //     return $model;
    // }
}
