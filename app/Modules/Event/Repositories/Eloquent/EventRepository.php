<?php

namespace App\Modules\Event\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Status;
use App\Modules\Customer\Models\Customer;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use App\Modules\Package\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use App\Modules\Payment\Models\Payment;

class EventRepository extends BaseRepository implements EventContract
{
    protected $status = null;

    public function __construct(Event $model)
    {
        parent::__construct($model);

        $this->status = Status::query();
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

        $eventData = $payload['event'] ?? [];
        $eventData['customer_id'] = $customer->id;

        $event = $this->model;
        $event->toFill($eventData);
        $event->save();

        $statusId = (clone $this->status)
                                    ->where('model', 'Event')
                                    ->where('name', 'Confirmed')
                                    ->value('id');

        $logStatus['status_id'] = $statusId;
        $logStatus['model_id'] = $event->id;
        $logStatus['model_type'] = $event->getMorphClass();

        $log = $event->statusLogs()->firstOrNew();
        $log->toFill($logStatus);
        $log->save();

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
        $order->status_id = (clone $this->status)
                            ->where('model', 'Order')
                            ->where('name', 'Confirmed')
                            ->value('id');
        $order->save();
        $order->refresh();

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

        $invoice->order_id = $order->id;
        $invoice->event_id = $event->id;
        $invoice->issued_at = today();
        $invoice->due_at = today();
        $invoice->outstanding_amount = $payable;
        $invoice->discount = 0;
        $invoice->payable_amount = $advanceAmount;
        $invoice->save();
        $invoice->refresh();

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

            $paymentStatusId = (clone $this->status)
                                ->where('model', 'Payment')
                                ->where('name', 'Completed')
                                ->value('id');

            if($payment->status_id === $paymentStatusId) {
                $statusId = (clone $this->status)
                                ->where('model', 'Invoice')
                                ->where('name', 'Paid')
                                ->value('id');

                $invoice->status_id = $statusId;
                $invoice->save();

                $outstandingAmount = $order->payable_amount - $invoice->payable_amount;

                $order->payable_amount = $outstandingAmount;
                $order->save();
            }
        }

        return $event;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // 1. Update Customer
        $customer = $model->customer;

        $customer->toFill($payload['customer']);
        $customer->save();

        // 2. Update Event
        $event = $model;

        $eventData = $payload['event'] ?? [];
        $eventData['customer_id'] = $customer->id;

        $event->toFill($eventData);
        $event->save();

        $logStatus['status_id'] = $payload['status_id'];
        $logStatus['model_id'] = $event->id;
        $logStatus['model_type'] = $event->getMorphClass();

        $log = $event->statusLogs()->firstOrNew();
        $log->toFill($logStatus);
        $log->save();

        // 3. Calculate Order Items
        $lines = [];
        $grandTotal = 0;

        foreach ($payload['items'] ?? [] as $item) {

            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $subtotal = $quantity * $unitPrice;

            $lines[] = [
                'inventory_item_id' => $item['inventory_item_id'],
                'service_id' => null,
                'package_id' => null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];

            $grandTotal += $subtotal;
        }

        foreach ($payload['services'] ?? [] as $service) {

            $quantity = (float) $service['quantity'];
            $unitPrice = (float) $service['unit_price'];
            $subtotal = $quantity * $unitPrice;

            $lines[] = [
                'inventory_item_id' => null,
                'service_id' => $service['service_id'],
                'package_id' => null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];

            $grandTotal += $subtotal;
        }

        if (!empty($payload['package_id'])) {

            $package = Package::findOrFail($payload['package_id']);

            $lines[] = [
                'inventory_item_id' => null,
                'service_id' => null,
                'package_id' => $package->id,
                'quantity' => 1,
                'unit_price' => $package->price,
                'subtotal' => $package->price,
            ];

            $grandTotal += $package->price;
        }

        // 4. Update Order
        $discount = max(
            0,
            min(
                (float) ($payload['payment']['discount'] ?? 0),
                $grandTotal
            )
        );

        $securityDeposit = max(
            0,
            (float) ($payload['payment']['security_deposit'] ?? 0)
        );

        $advanceAmount = max(
            0,
            (float) ($payload['payment']['advance_amount'] ?? 0)
        );

        $payable = $grandTotal - $discount;

        $order = $event->order;

        $order->event_id = $event->id;
        $order->grand_total = $grandTotal;
        $order->discount = $discount;
        $order->security_deposit = $securityDeposit;

        $order->status_id = (clone $this->status)
                                ->where('model', 'Order')
                                ->where('name', 'Confirmed')
                                ->value('id');

        $order->save();

        // 5. Synchronize Order Items
        $existingItemIds = [];

        foreach ($lines as $line) {

            $orderItem = OrderItem::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'inventory_item_id' => $line['inventory_item_id'],
                    'service_id' => $line['service_id'],
                    'package_id' => $line['package_id'],
                ],
                array_merge($line, [
                    'order_id' => $order->id,
                ])
            );

            $existingItemIds[] = $orderItem->id;
        }

        // Remove items deleted from the edit form
        $order->orderItems()
            ->whereNotIn('id', $existingItemIds)
            ->delete();

        // 6. Update Invoice
        $invoice = $event->invoice;

        $invoice->order_id = $order->id;
        $invoice->event_id = $event->id;
        $invoice->issued_at = $invoice->issued_at ?? today();
        $invoice->due_at = $invoice->due_at ?? today();
        $invoice->outstanding_amount = $payable;
        $invoice->discount = 0;
        $invoice->payable_amount = $advanceAmount;

        // 7. Update Payment
        $payment = $event->payment;

        if ($advanceAmount > 0) {

            if (!$payment) {
                $payment = new Payment();
            }

            $payment->invoice_id = $invoice->id;
            $payment->payment_method_id =
                $payload['payment']['payment_method_id'];

            $payment->amount = min($advanceAmount, $payable);
            $payment->paid_at = $payment->paid_at ?? now();
            $payment->note = $payload['event']['note'] ?? null;

            $payment->status_id = (clone $this->status)
                                    ->where('model', 'Payment')
                                    ->where('name', 'Completed')
                                    ->value('id');

            $payment->save();

        } elseif ($payment) {

            // Only remove an existing advance payment if
            // your business rules allow editing/deleting it.
            $payment->delete();
        }

        // 8. Recalculate Financial Summary
        $invoice->status_id = (clone $this->status)
                                ->where('model', 'Invoice')
                                ->where('name', 'Paid')
                                ->value('id');
        $invoice->save();

        $outstanding = max(0, $payable - $advanceAmount);
        $order->payable_amount = $outstanding;
        $order->save();

        return $event->refresh();
    }

    public function storePayment(Model $model, array $payload): Model
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Payment Information
        |--------------------------------------------------------------------------
        */

        $amount = round((float) $payload['amount'], 2);
        $discount = round((float) ($payload['discount'] ?? 0), 2);

        /*
        |--------------------------------------------------------------------------
        | 2. Calculate Event Outstanding
        |--------------------------------------------------------------------------
        */

        $totalOrderAmount = (float) $model->orders()
            ->sum('payable_amount');

        // Total completed payments
        $completedStatusId = (clone $this->status)
            ->where('model', 'Payment')
            ->where('name', 'Completed')
            ->value('id');

        $totalPaid = (float) $model->payments()
            ->where('payments.status_id', $completedStatusId)
            ->sum('payments.amount');

        // Total previously applied invoice discounts
        $totalInvoiceDiscount = (float) $model->invoices()
            ->sum('discount');

        $outstandingAmount = round(max(
            0,
            $totalOrderAmount - $totalPaid - $totalInvoiceDiscount
        ), 2);

        /*
        |--------------------------------------------------------------------------
        | 3. Validate Payment and Discount
        |--------------------------------------------------------------------------
        */

        $submittedAmount = round($amount + $discount, 2);

        if ($submittedAmount > $outstandingAmount) {
            throw ValidationException::withMessages([
                'amount' => 'Payment and discount cannot exceed the outstanding amount.',
            ]);
        }

        if ($submittedAmount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payment or discount must be greater than zero.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Calculate Remaining Outstanding
        |--------------------------------------------------------------------------
        */

        $remainingOutstanding = round(max(
            0,
            $outstandingAmount - $submittedAmount
        ), 2);

        /*
        |--------------------------------------------------------------------------
        | 5. Create Invoice
        |--------------------------------------------------------------------------
        */

        $invoice = new Invoice();

        $invoice->event_id = $model->id;
        $invoice->issued_at = today();
        $invoice->due_at = today();

        // Invoice amount before discount
        $invoice->outstanding_amount = $submittedAmount;

        $invoice->discount = $discount;
        $invoice->payable_amount = $amount;

        // Remaining event outstanding
        $invoice->outstanding_amount = $remainingOutstanding;

        $invoice->save();

        /*
        |--------------------------------------------------------------------------
        | 6. Record Payment
        |--------------------------------------------------------------------------
        */

        if ($amount > 0) {

            $payment = new Payment();

            $payment->status_id = $completedStatusId;
            $payment->invoice_id = $invoice->id;
            $payment->payment_method_id = $payload['payment_method_id'];
            $payment->amount = $amount;
            $payment->paid_at = $payload['paid_at'] ?? now();
            $payment->note = $payload['payment_note'] ?? null;

            $payment->save();
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Update Invoice Status
        |--------------------------------------------------------------------------
        */

        $paidStatusId = (clone $this->status)
            ->where('model', 'Invoice')
            ->where('name', 'Paid')
            ->value('id');

        $invoice->status_id = $paidStatusId;
        $invoice->save();

        /*
        |--------------------------------------------------------------------------
        | Update Orders Payable Amount
        |--------------------------------------------------------------------------
        */
        if ($remainingOutstanding <= 0) {
            $model->orders()->update([
                'payable_amount' => 0,
            ]);
        }

        return $model->refresh();
    }
}
