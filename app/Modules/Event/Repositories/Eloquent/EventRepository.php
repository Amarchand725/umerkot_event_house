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
use App\Modules\OrderReturn\Models\OrderReturn;
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
            $quantity = $item['quantity'];
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
            $lineTotal = max(0, (float) $package->price - (float) $package->discount);

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

        if ($securityDeposit < 0 || $advanceAmount < 0 || $advanceAmount > $payable) {
            throw ValidationException::withMessages([
                'payment.advance_amount' => 'Advance must be between zero and the payable amount.',
            ]);
        }

        if ($advanceAmount > 0 && empty($payload['payment']['payment_method_id'])) {
            throw ValidationException::withMessages([
                'payment.payment_method_id' => 'Please select a payment method.',
            ]);
        }
        //security deposit is not part of the total, it's an additional amount to be paid upfront
        $order->security_deposit = $securityDeposit;

        $order->event_id = $event->id;
        $order->grand_total = $grandTotal;
        $order->discount = $discount;
        $order->payable_amount = max(0, $payable - $advanceAmount);
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
        $invoice->outstanding_amount = max(0, $payable - $advanceAmount);
        $invoice->discount = 0;
        $invoice->payable_amount = $payable;
        $invoice->status_id = (clone $this->status)
            ->where('model', 'Invoice')
            ->where('name', $invoice->outstanding_amount <= 0 ? 'Paid' : 'Pending')
            ->value('id');
        $invoice->save();
        $invoice->refresh();

        if ($advanceAmount > 0) {
            /*
            |--------------------------------------------------------------------------
            | 7. Record Payment
            |--------------------------------------------------------------------------
            */

            $payment = new Payment();
            $payment->invoice_id = $invoice->id;
            $payment->payment_method_id = $payload['payment']['payment_method_id'];
            $payment->discount = 0;
            $payment->amount = $advanceAmount;
            $payment->paid_at = now();
            $payment->note = $payload['event']['note'] ?? null;
            $payment->status_id = (clone $this->status)
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');
            $payment->save();

            $paymentStatusId = (clone $this->status)
                                ->where('model', 'Payment')
                                ->where('name', 'Completed')
                                ->value('id');

            $paidAmount = (float) $invoice->payments()
                ->where('status_id', $paymentStatusId)
                ->sum('amount');
            $invoiceStatus = $invoice->outstanding_amount <= 0
                ? 'Paid'
                : ($paidAmount > 0 ? 'Partial' : 'Pending');
            $invoice->status_id = (clone $this->status)
                ->where('model', 'Invoice')
                ->where('name', $invoiceStatus)
                ->value('id');
            $invoice->save();
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
                'unit_price' => max(0, (float) $package->price - (float) $package->discount),
                'subtotal' => max(0, (float) $package->price - (float) $package->discount),
            ];

            $grandTotal += max(0, (float) $package->price - (float) $package->discount);
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

        if ($advanceAmount > $payable) {
            throw ValidationException::withMessages([
                'payment.advance_amount' => 'Advance cannot exceed the order payable amount.',
            ]);
        }

        $order = $event->order;

        $order->event_id = $event->id;
        $order->grand_total = $grandTotal;
        $order->discount = $discount;
        $order->payable_amount = $payable;
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
        $invoice->payable_amount = $payable;

        // 7. Update Payment
        $payment = $event->payment;

        if ($advanceAmount > 0) {

            if (!$payment) {
                $payment = new Payment();
            }

            $payment->invoice_id = $invoice->id;
            $payment->payment_method_id =
                $payload['payment']['payment_method_id'];

            $payment->discount = 0;
            $payment->amount = min($advanceAmount, $payable);
            $payment->paid_at = $payment->paid_at ?? now();
            $payment->note = $payload['event']['note'] ?? null;

            $payment->status_id = (clone $this->status)
                                    ->where('model', 'Payment')
                                    ->where('name', 'Completed')
                                    ->value('id');

            $payment->save();

        }

        // 8. Recalculate Financial Summary
        $paidAmount = (float) $invoice->payments()
            ->whereHas('status', fn ($query) => $query->where('name', 'Completed'))
            ->sum('amount');
        if ($paidAmount > $payable) {
            throw ValidationException::withMessages([
                'payment.discount' => 'The updated order total cannot be less than payments already received.',
            ]);
        }
        $invoice->outstanding_amount = max(0, round($payable - $paidAmount, 2));
        $order->payable_amount = $invoice->outstanding_amount;
        $invoiceStatus = $invoice->outstanding_amount <= 0
            ? 'Paid'
            : ($paidAmount > 0 ? 'Partial' : 'Pending');
        $invoice->status_id = (clone $this->status)
            ->where('model', 'Invoice')
            ->where('name', $invoiceStatus)
            ->value('id');
        if ($invoiceStatus === 'Paid') {
            $order->status_id = (clone $this->status)
                ->where('model', 'Order')
                ->where('name', 'Completed')
                ->value('id');
        }
        $order->save();
        $invoice->save();

        // $outstanding = max(0, $payable - $advanceAmount);
        // $order->payable_amount = $outstanding;
        // $order->save();

        return $event->refresh();
    }

    public function storePayment(Model $model, array $payload): Model
    {
        if (!$model instanceof Event) {
            throw new \InvalidArgumentException('Expected an Event model.');
        }

        return app(\App\Modules\Payment\Services\PaymentSettlementService::class)
            ->settle($model, $payload);
    }

    public function updateStatus(Model $model, array $payload)
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
                'unit_price' => max(0, $packagePrice - (float) $package->discount),
                'subtotal' => max(0, $packagePrice - (float) $package->discount),
                ];

                $grandTotal += max(0, $packagePrice - (float) $package->discount);
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

            $invoice = new Invoice();
            $invoice->order_id = $order->id;
            $invoice->event_id = $event->id;
            $invoice->issued_at = today();
            $invoice->due_at = today();
            $invoice->discount = 0;
            $invoice->payable_amount = $payable;
            $invoice->outstanding_amount = round($payable - $advanceAmount, 2);

            $paymentStatusId = (clone $this->status)
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');

            if (!$paymentStatusId) {
                throw new \RuntimeException('Completed payment status was not found.');
            }

            $invoiceStatusName = $invoice->outstanding_amount <= 0
                ? 'Paid'
                : ($advanceAmount > 0 ? 'Partial' : 'Pending');
            $invoice->status_id = (clone $this->status)
                ->where('model', 'Invoice')
                ->where('name', $invoiceStatusName)
                ->value('id');
            $invoice->save();

            if ($advanceAmount > 0) {
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
                // 'return_number' => app(
                //     \App\Services\DocumentNumberService::class
                // )->generate('order_return'),
            ]);

            foreach ($preparedItems as $itemData) {
                $return->items()->create($itemData);
            }

            return $return->load('items');
        });
    }
}
