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
use Illuminate\Support\Facades\DB;

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

        if(isset($payload['package_id']) && !empty($payload['package_id'])) {
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

        if ($advanceAmount > 0) {
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
        return DB::transaction(function () use ($model, $payload) {

            // 1. Lock the event to prevent concurrent balance updates.
            $event = $model->newQuery()
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Calculate order totals.
            $orders = $event->orders()
                ->with('invoice')
                ->get();

            $totalOrderAmount = (float) $orders->sum('grand_total');
            $totalOrderDiscount = (float) $orders->sum('discount');

            // 3. Get invoice discounts.
            $totalInvoiceDiscount = (float) $event->invoices()
                ->sum('discount');

            // IMPORTANT: grand_total must be before order discount.
            $totalPayable = round(
                $totalOrderAmount
                - $totalOrderDiscount
                - $totalInvoiceDiscount,
                2
            );

            // 4. Only completed payments count as received money.
            $completedStatusId = (clone $this->status)
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');

            if (!$completedStatusId) {
                throw new \RuntimeException(
                    'Completed payment status is not configured.'
                );
            }

            $totalPaid = (float) $event->payments()
                ->where('payments.status_id', $completedStatusId)
                ->sum('payments.amount');

            $outstanding = round(
                max(0, $totalPayable - $totalPaid),
                2
            );

            // 5. Validate transaction amounts.
            $amount = round((float) ($payload['amount'] ?? 0), 2);
            $discount = round((float) ($payload['discount'] ?? 0), 2);

            if ($amount < 0 || $discount < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment and discount cannot be negative.',
                ]);
            }

            $transactionTotal = round($amount + $discount, 2);

            if ($transactionTotal <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment or discount must be greater than zero.',
                ]);
            }

            if ($transactionTotal > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment and discount exceed the outstanding balance.',
                ]);
            }

            // 6. Find or create the invoice.
            // This example uses one invoice per order.
            // A production implementation should explicitly select the
            // order being settled when an event has multiple orders.
            $order = $orders->first(function ($order) use ($amount, $discount) {
                return true;
            });

            if (!$order) {
                throw ValidationException::withMessages([
                    'amount' => 'No order is available for this event.',
                ]);
            }

            if ($amount > 0) {
                $invoice = $order->invoice;

                if (!$invoice) {
                    $invoice = new Invoice();
                    $invoice->event_id = $event->id;
                    $invoice->order_id = $order->id;
                    $invoice->issued_at = today();
                    $invoice->due_at = today();
                    $invoice->discount = 0;
                    $invoice->payable_amount = 0;
                    $invoice->outstanding_amount = 0;
                    $invoice->save();
                }

                // 7. Record the additional invoice discount.
                // Do not silently overwrite existing discounts.
                $invoice->discount = round(
                    (float) $invoice->discount + $discount,
                    2
                );

                // 8. Record the actual payment.

                $payment = new Payment();
                $payment->status_id = $completedStatusId;
                $payment->invoice_id = $invoice->id;
                $payment->payment_method_id = $payload['payment_method_id'];
                $payment->amount = $amount;
                $payment->paid_at = $payload['paid_at'] ?? now();
                $payment->note = $payload['payment_note'] ?? null;
                $payment->save();

                // 9. Recalculate this invoice's balances.
                $invoicePaid = (float) $invoice->payments()
                    ->where('payments.status_id', $completedStatusId)
                    ->sum('payments.amount');

                $invoiceGross = (float) $order->grand_total;
                $invoiceOrderDiscount = (float) $order->discount;

                $invoicePayable = round(
                    $invoiceGross
                    - $invoiceOrderDiscount
                    - (float) $invoice->discount,
                    2
                );

                $invoiceOutstanding = round(
                    max(0, $invoicePayable - $invoicePaid),
                    2
                );

                $invoice->payable_amount = $invoicePayable;
                $invoice->outstanding_amount = $invoiceOutstanding;

                $invoiceStatusName = $invoiceOutstanding <= 0
                    ? 'Paid'
                    : ($invoicePaid > 0 ? 'Partial' : 'Pending');

                $invoice->status_id = (clone $this->status)
                    ->where('model', 'Invoice')
                    ->where('name', $invoiceStatusName)
                    ->value('id');

                $invoice->save();

                // 10. Update order payable amount.
                $order->payable_amount = $invoiceOutstanding;
                $order->save();
            }

            return $event->refresh();
        }, 3);
    }

    public function updateStatus(Model $model, array $payload, bool $isResource = true)
    {
        $log = $model->statusLogs()->make();
        $log->toFill($payload);
        $log->save();

        return $model->refresh();
    }

    public function storeOrder(Model $model, array $payload): Model
    {
        return DB::transaction(function () use ($model, $payload) {
            $event = $model;

            /*
            |--------------------------------------------------------------------------
            | 1. Prepare inventory and service rows
            |--------------------------------------------------------------------------
            */

            $items = collect($payload['items'] ?? [])
                ->filter(fn ($item) => filled($item['inventory_item_id'] ?? null))
                ->values();

            $services = collect($payload['services'] ?? [])
                ->filter(fn ($service) => filled($service['service_id'] ?? null))
                ->values();

            $packageId = $payload['package_id'] ?? null;

            if (blank($packageId) && $items->isEmpty() && $services->isEmpty()) {
                throw ValidationException::withMessages([
                    'order' => 'Please select a package, an inventory item, or a service.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Build order lines and calculate total
            |--------------------------------------------------------------------------
            */

            $lines = [];
            $grandTotal = 0;

            foreach ($items as $index => $item) {
                if (
                    !isset($item['quantity'], $item['unit_price']) ||
                    !is_numeric($item['quantity']) ||
                    !is_numeric($item['unit_price']) ||
                    (float) $item['quantity'] <= 0 ||
                    (float) $item['unit_price'] < 0
                ) {
                    throw ValidationException::withMessages([
                        "items.$index" => 'Inventory quantity and unit price must be valid.',
                    ]);
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $lineTotal = round($quantity * $unitPrice, 2);

                $lines[] = [
                    'inventory_item_id' => $item['inventory_item_id'],
                    'service_id' => null,
                    'package_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineTotal,
                ];

                $grandTotal += $lineTotal;
            }

            foreach ($services as $index => $service) {
                if (
                    !isset($service['quantity'], $service['unit_price']) ||
                    !is_numeric($service['quantity']) ||
                    !is_numeric($service['unit_price']) ||
                    (float) $service['quantity'] <= 0 ||
                    (float) $service['unit_price'] < 0
                ) {
                    throw ValidationException::withMessages([
                        "services.$index" => 'Service quantity and unit price must be valid.',
                    ]);
                }

                $quantity = (float) $service['quantity'];
                $unitPrice = (float) $service['unit_price'];
                $lineTotal = round($quantity * $unitPrice, 2);

                $lines[] = [
                    'inventory_item_id' => null,
                    'service_id' => $service['service_id'],
                    'package_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineTotal,
                ];

                $grandTotal += $lineTotal;
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Add package if selected
            |--------------------------------------------------------------------------
            */

            if (filled($packageId)) {
                $package = Package::query()->find($packageId);

                if (!$package) {
                    throw ValidationException::withMessages([
                        'package_id' => 'The selected package does not exist.',
                    ]);
                }

                $packagePrice = round((float) $package->price, 2);

                $lines[] = [
                    'inventory_item_id' => null,
                    'service_id' => null,
                    'package_id' => $package->id,
                    'quantity' => 1,
                    'unit_price' => $packagePrice,
                    'subtotal' => $packagePrice,
                ];

                $grandTotal += $packagePrice;
            }

            $grandTotal = round($grandTotal, 2);

            /*
            |--------------------------------------------------------------------------
            | 4. Calculate discount, deposit and advance
            |--------------------------------------------------------------------------
            */

            $paymentData = $payload['payment'] ?? [];

            $discount = round((float) ($paymentData['discount'] ?? 0), 2);
            $securityDeposit = round(
                (float) ($paymentData['security_deposit'] ?? 0),
                2
            );
            $advanceAmount = round(
                (float) ($paymentData['advance_amount'] ?? 0),
                2
            );

            if ($discount < 0 || $securityDeposit < 0 || $advanceAmount < 0) {
                throw ValidationException::withMessages([
                    'payment' => 'Discount, security deposit and advance cannot be negative.',
                ]);
            }

            $discount = min($discount, $grandTotal);
            $payable = round($grandTotal - $discount, 2);

            if ($advanceAmount > $payable) {
                throw ValidationException::withMessages([
                    'payment.advance_amount' => 'Advance cannot exceed the order payable amount.',
                ]);
            }

            if (
                $advanceAmount > 0 &&
                empty($paymentData['payment_method_id'])
            ) {
                throw ValidationException::withMessages([
                    'payment.payment_method_id' => 'Please select a payment method.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Create order
            |--------------------------------------------------------------------------
            */

            $confirmedStatusId = (clone $this->status)
                ->where('model', 'Order')
                ->where('name', 'Confirmed')
                ->value('id');

            if (!$confirmedStatusId) {
                throw new \RuntimeException('Confirmed order status was not found.');
            }

            $order = new Order();

            $order->event_id = $event->id;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->payable_amount = round($payable - $advanceAmount, 2);
            $order->security_deposit = $securityDeposit;
            $order->status_id = $confirmedStatusId;

            // Keep any other required order fields from your existing code,
            // such as order_number, note or package_id, if applicable.

            $order->save();

            /*
            |--------------------------------------------------------------------------
            | 6. Save order lines
            |--------------------------------------------------------------------------
            */

            foreach ($lines as $line) {
                $line['order_id'] = $order->id;

                OrderItem::create($line);
            }

            /*
            |--------------------------------------------------------------------------
            | 7. Create invoice and record advance payment
            |--------------------------------------------------------------------------
            */

            if ($advanceAmount > 0) {
                $invoice = new Invoice();

                $invoice->order_id = $order->id;
                $invoice->event_id = $event->id;
                $invoice->issued_at = today();
                $invoice->due_at = today();

                // Invoice represents the complete discounted order.
                $invoice->discount = $discount;
                $invoice->payable_amount = $payable;
                $invoice->outstanding_amount = round(
                    $payable - $advanceAmount,
                    2
                );

                $paymentStatusId = (clone $this->status)
                    ->where('model', 'Payment')
                    ->where('name', 'Completed')
                    ->value('id');

                $invoiceStatusId = (clone $this->status)
                    ->where('model', 'Invoice')
                    ->where('name', 'Paid')
                    ->value('id');

                if (!$paymentStatusId || !$invoiceStatusId) {
                    throw new \RuntimeException(
                        'Required payment or invoice status was not found.'
                    );
                }

                if ($invoice->outstanding_amount <= 0) {
                    $invoice->status_id = $invoiceStatusId;
                }

                $invoice->save();

                $payment = new Payment();

                $payment->invoice_id = $invoice->id;
                $payment->payment_method_id = $paymentData['payment_method_id'];
                $payment->amount = $advanceAmount;
                $payment->paid_at = now();
                $payment->note = $payload['event']['note'] ?? null;
                $payment->status_id = $paymentStatusId;

                $payment->save();
            }

            return $event;
        });
    }

    public function storeReturnOrder(Model $model, array $payload): Model
    {
        if (! $model instanceof Event) {
            throw new \InvalidArgumentException(
                'Expected an Event model.'
            );
        }

        return DB::transaction(function () use ($model, $payload) {
            $event = Event::query()
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $orders = $event->orders()
                ->with('orderItems.item')
                ->lockForUpdate()
                ->get();

            $submittedItems = collect($payload['items'] ?? []);

            if ($submittedItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'At least one item must be inspected.',
                ]);
            }

            $orderIds = $orders->modelKeys();

            $actualItems = $orders->flatMap(
                fn ($order) => $order->orderItems
            )->keyBy(fn ($item) => (string) $item->getKey());

            $goodTotal = 0;
            $damageTotal = 0;
            $missingTotal = 0;
            $preparedItems = [];

            foreach ($submittedItems as $submitted) {
                $orderItemId = (string) ($submitted['order_item_id'] ?? '');
                $orderId = $submitted['order_id'] ?? null;

                $orderItem = $actualItems->get($orderItemId);

                if (
                    ! $orderItem ||
                    (string) $orderItem->order_id !== (string) $orderId ||
                    ! in_array($orderItem->order_id, $orderIds)
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'An order item does not belong to this event.',
                    ]);
                }

                if (isset($preparedItems[$orderItemId])) {
                    throw ValidationException::withMessages([
                        'items' => 'Duplicate order items are not allowed.',
                    ]);
                }

                $issued = (int) $orderItem->quantity;
                $good = (int) ($submitted['good_quantity'] ?? 0);
                $damaged = (int) ($submitted['damaged_quantity'] ?? 0);
                $missing = (int) ($submitted['missing_quantity'] ?? 0);

                if (
                    min($good, $damaged, $missing) < 0 ||
                    ($good + $damaged + $missing) !== $issued
                ) {
                    throw ValidationException::withMessages([
                        "items.{$orderItemId}" =>
                            "Good, damaged, and missing quantities must total {$issued}.",
                    ]);
                }

                $item = $orderItem->item;

                $damageRate = (float) ($item?->damage_charge ?? 0);
                $replacementRate = (float) ($item?->replacement_charge ?? 0);

                $damageAmount = round($damaged * $damageRate, 2);
                $missingAmount = round($missing * $replacementRate, 2);

                $damageTotal += $damageAmount;
                $missingTotal += $missingAmount;

                $preparedItems[$orderItemId] = [
                    'order_id' => $orderItem->order_id,
                    'order_item_id' => $orderItem->getKey(),
                    'issued_quantity' => $issued,
                    'good_quantity' => $good,
                    'damaged_quantity' => $damaged,
                    'missing_quantity' => $missing,
                    'damage_charge' => $damageRate,
                    'replacement_charge' => $replacementRate,
                    'deduction_amount' => round(
                        $damageAmount + $missingAmount,
                        2
                    ),
                    'inspection_notes' =>
                        $submitted['inspection_notes'] ?? null,
                ];
            }

            // Require every order item to be inspected.
            if (count($preparedItems) !== $actualItems->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Please inspect every item from every order.',
                ]);
            }

            $securityDeposit = round(
                (float) $orders->sum('security_deposit'),
                2
            );

            $totalDamage = round($damageTotal, 2);
            $totalMissing = round($missingTotal, 2);
            $totalDeduction = round($totalDamage + $totalMissing, 2);

            $refundableDeposit = round(
                max(0, $securityDeposit - $totalDeduction),
                2
            );

            $additionalAmountDue = round(
                max(0, $totalDeduction - $securityDeposit),
                2
            );

            $return = OrderReturn::create([
                'event_id' => $event->getKey(),
                'inspected_by' => auth()->id(),
                'returned_at' => now(),
                'notes' => $payload['notes'] ?? null,

                // Replace with your actual document number generator.
                'return_number' => app(
                    \App\Services\DocumentNumberService::class
                )->generate('order_return'),
            ]);

            foreach ($preparedItems as $itemData) {
                $return->items()->create($itemData);
            }

            return $return->load('items');
        });
    }
}
