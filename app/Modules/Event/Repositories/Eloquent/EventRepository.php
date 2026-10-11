<?php

namespace App\Modules\Event\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\PackageItem;
use App\Models\Status;
use App\Modules\Customer\Models\Customer;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use App\Modules\Package\Models\Package;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class EventRepository extends BaseRepository implements EventContract
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

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
        $eventData = $payload['event'] ?? [];
        $eventData['customer_id'] = $customer->id;
        $eventData['status_id'] = Status::where('model', 'Event')
            ->where('name', 'Confirmed')
            ->value('id');

        $event->toFill($eventData);
        $event->save();

        /*
        |--------------------------------------------------------------------------
        | 3. Calculate Order Items and Total
        |--------------------------------------------------------------------------
        */

        $lines = [];
        $grandTotal = 0;

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

            $grandTotal += $lineTotal;
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

            $grandTotal += $lineTotal;
        }

        if($payload['package_id']) {
            $package = Package::where('id', $payload['package_id'])->first();
            $lineTotal = $package->price; // Assuming the package has a price attribute

            $lines[] = [
                'package_id'        => $package->id,
                'unit_price'        => $lineTotal,
                'subtotal'          => $lineTotal,
            ];

            $grandTotal += $lineTotal;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Create Order
        |--------------------------------------------------------------------------
        */

        $order = new Order();

        /*
        |--------------------------------------------------------------------------
        | 5. Calculate Invoice
        |--------------------------------------------------------------------------
        */

        $discount = (float) ($payload['payment']['discount'] ?? 0);
        $securityDeposit = (float) ($payload['payment']['security_deposit'] ?? 0);
        $advanceAmount = (float) ($payload['payment']['advance_amount'] ?? 0);

        // Assumes discount is a fixed monetary amount.
        $discount = min($discount, $grandTotal);

        $payable = $grandTotal - $discount;
        //security deposit is not part of the total, it's an additional amount to be paid upfront
        $order->security_deposit = $securityDeposit;

        $order->event_id = $event->id;
        $order->grand_total = $grandTotal;
        $order->discount = $discount;
        $order->payable_amount = $payable;
        $order->status_id = Status::where('model', 'Order')
            ->where('name', 'Confirmed')
            ->value('id');
        $order->save();

        foreach ($lines as $line) {
            $line['order_id'] = $order->id;
            OrderItem::create($line);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Create Invoice
        |--------------------------------------------------------------------------
        */

        $invoice = new Invoice();

        $invoice->event_id = $event->id;
        $invoice->issued_at = today();
        $invoice->due_at = today();
        $invoice->outstanding_amount = $payable;
        $invoice->discount = 0;
        $invoice->payable_amount = $advanceAmount;
        $invoice->save();

        /*
        |--------------------------------------------------------------------------
        | 7. Record Payment
        |--------------------------------------------------------------------------
        */

        if ($advanceAmount > 0) {
            $payment = new Payment();
            $payment->invoice_id = $invoice->id;
            $payment->payment_method_id = $payload['payment']['payment_method_id'];
            $payment->amount = $advanceAmount;
            $payment->paid_at = now();
            $payment->note = $payload['event']['note'] ?? null;
            $payment->save();

            $paymentStatusId = Status::where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');

            if($payment->status_id === $paymentStatusId) {
                $statusId = Status::where('model', 'Invoice')
                    ->where('name', 'Paid')
                    ->value('id');

                $invoice->outstanding_amount -= $advanceAmount;
                $invoice->status_id = $statusId;
                $invoice->save();
            }
        }

        return $event;
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
