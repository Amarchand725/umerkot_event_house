<?php

namespace App\Modules\Employee\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Employee\Repositories\Contracts\EmployeeContract;
use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Models\Refrence;

class EmployeeRepository extends BaseRepository implements EmployeeContract
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model->make();
        $model->toFill($payload, ['compensation', 'reference']);
        $model->save();

        if($payload['reference']){
            $refrence = Refrence::firstOrCreate(
                [
                    'full_name' => $payload['reference']['full_name'],
                    'phone' => $payload['reference']['phone'],
                ],
                [
                    'address' => $payload['reference']['address'] ?? null,
                ]
            );

            if($refrence){
                $model->referred_by_id = $refrence->id;
                $model->save();
            }
        }

        // Compensation
        if (!empty($payload['compensation'])) {
            $model->compensation()->create($payload['compensation']);
        }

        return $model;
    }

    // public function updateModel(Model $model, array $payload): Model
    // {
    //     // 1. Update Customer
    //     $customer = $model->customer;

    //     $customer->toFill($payload['customer']);
    //     $customer->save();

    //     // 2. Update Event
    //     $event = $model;

    //     $eventData = $payload['event'] ?? [];
    //     $eventData['customer_id'] = $customer->id;

    //     $event->toFill($eventData);
    //     $event->save();

    //     $logStatus['status_id'] = $payload['status_id'];
    //     $logStatus['model_id'] = $event->id;
    //     $logStatus['model_type'] = $event->getMorphClass();

    //     $log = $event->statusLogs()->firstOrNew();
    //     $log->toFill($logStatus);
    //     $log->save();

    //     // 3. Calculate Order Items
    //     $lines = [];
    //     $grandTotal = 0;

    //     foreach ($payload['items'] ?? [] as $item) {

    //         $quantity = (float) $item['quantity'];
    //         $unitPrice = (float) $item['unit_price'];
    //         $subtotal = $quantity * $unitPrice;

    //         $lines[] = [
    //             'inventory_item_id' => $item['inventory_item_id'],
    //             'service_id' => null,
    //             'package_id' => null,
    //             'quantity' => $quantity,
    //             'unit_price' => $unitPrice,
    //             'subtotal' => $subtotal,
    //         ];

    //         $grandTotal += $subtotal;
    //     }

    //     foreach ($payload['services'] ?? [] as $service) {

    //         $quantity = (float) $service['quantity'];
    //         $unitPrice = (float) $service['unit_price'];
    //         $subtotal = $quantity * $unitPrice;

    //         $lines[] = [
    //             'inventory_item_id' => null,
    //             'service_id' => $service['service_id'],
    //             'package_id' => null,
    //             'quantity' => $quantity,
    //             'unit_price' => $unitPrice,
    //             'subtotal' => $subtotal,
    //         ];

    //         $grandTotal += $subtotal;
    //     }

    //     if (!empty($payload['package_id'])) {

    //         $package = Package::findOrFail($payload['package_id']);

    //         $lines[] = [
    //             'inventory_item_id' => null,
    //             'service_id' => null,
    //             'package_id' => $package->id,
    //             'quantity' => 1,
    //             'unit_price' => $package->price,
    //             'subtotal' => $package->price,
    //         ];

    //         $grandTotal += $package->price;
    //     }

    //     // 4. Update Order
    //     $discount = max(
    //         0,
    //         min(
    //             (float) ($payload['payment']['discount'] ?? 0),
    //             $grandTotal
    //         )
    //     );

    //     $securityDeposit = max(
    //         0,
    //         (float) ($payload['payment']['security_deposit'] ?? 0)
    //     );

    //     $advanceAmount = max(
    //         0,
    //         (float) ($payload['payment']['advance_amount'] ?? 0)
    //     );

    //     $payable = $grandTotal - $discount;

    //     $order = $event->order;

    //     $order->event_id = $event->id;
    //     $order->grand_total = $grandTotal;
    //     // $order->discount = $discount;
    //     $order->payable_amount = $payable;
    //     $order->security_deposit = $securityDeposit;

    //     $order->status_id = (clone $this->status)
    //                             ->where('model', 'Order')
    //                             ->where('name', 'Confirmed')
    //                             ->value('id');

    //     $order->save();

    //     // 5. Synchronize Order Items
    //     $existingItemIds = [];

    //     foreach ($lines as $line) {

    //         $orderItem = OrderItem::updateOrCreate(
    //             [
    //                 'order_id' => $order->id,
    //                 'inventory_item_id' => $line['inventory_item_id'],
    //                 'service_id' => $line['service_id'],
    //                 'package_id' => $line['package_id'],
    //             ],
    //             array_merge($line, [
    //                 'order_id' => $order->id,
    //             ])
    //         );

    //         $existingItemIds[] = $orderItem->id;
    //     }

    //     // Remove items deleted from the edit form
    //     $order->orderItems()
    //         ->whereNotIn('id', $existingItemIds)
    //         ->delete();

    //     // 6. Update Invoice
    //     $invoice = $event->invoice;

    //     $invoice->order_id = $order->id;
    //     $invoice->event_id = $event->id;
    //     $invoice->issued_at = $invoice->issued_at ?? today();
    //     $invoice->due_at = $invoice->due_at ?? today();
    //     $invoice->outstanding_amount = $payable;
    //     $invoice->discount = $discount;
    //     $invoice->payable_amount = $advanceAmount;

    //     // 7. Update Payment
    //     $payment = $event->payment;

    //     if ($advanceAmount > 0) {

    //         if (!$payment) {
    //             $payment = new Payment();
    //         }

    //         $payment->invoice_id = $invoice->id;
    //         $payment->payment_method_id =
    //             $payload['payment']['payment_method_id'];

    //         $payment->discount = $discount;
    //         $payment->amount = min($advanceAmount, $payable);
    //         $payment->paid_at = $payment->paid_at ?? now();
    //         $payment->note = $payload['event']['note'] ?? null;

    //         $payment->status_id = (clone $this->status)
    //                                 ->where('model', 'Payment')
    //                                 ->where('name', 'Completed')
    //                                 ->value('id');

    //         $payment->save();

    //     } elseif ($payment) {

    //         // Only remove an existing advance payment if
    //         // your business rules allow editing/deleting it.
    //         $payment->delete();
    //     }

    //     // 8. Recalculate Financial Summary
    //     $invoice->status_id = (clone $this->status)
    //                             ->where('model', 'Invoice')
    //                             ->where('name', 'Paid')
    //                             ->value('id');
    //     $invoice->save();

    //     // $outstanding = max(0, $payable - $advanceAmount);
    //     // $order->payable_amount = $outstanding;
    //     // $order->save();

    //     return $event->refresh();
    // }
}
