<?php

namespace App\Modules\Payment\Services;

use App\Models\Invoice;
use App\Models\Status;
use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentSettlementService
{
    public function refreshInvoice(Invoice $invoice): Invoice
    {
        $invoice = Invoice::query()
            ->whereKey($invoice->getKey())
            ->lockForUpdate()
            ->firstOrFail();
        $order = Order::query()->whereKey($invoice->order_id)->lockForUpdate()->firstOrFail();
        $completedStatusId = Status::query()
            ->where('model', 'Payment')
            ->where('name', 'Completed')
            ->value('id');

        $completedPayments = $invoice->payments()->where('status_id', $completedStatusId);
        $paidAmount = (float) (clone $completedPayments)->sum('amount');
        $invoiceDiscount = (float) $invoice->discount;
        $payable = max(
            0,
            round((float) $order->grand_total - (float) $order->discount - $invoiceDiscount, 2)
        );
        $outstanding = max(0, round($payable - $paidAmount, 2));
        $statusName = $outstanding <= 0
            ? 'Paid'
            : ($paidAmount > 0 ? 'Partial' : 'Pending');

        $invoice->discount = $invoiceDiscount;
        $invoice->payable_amount = $payable;
        $invoice->outstanding_amount = $outstanding;
        $invoice->status_id = Status::query()
            ->where('model', 'Invoice')
            ->where('name', $statusName)
            ->value('id');
        $invoice->save();

        $order->payable_amount = $outstanding;
        if ($statusName === 'Paid') {
            $order->status_id = Status::query()
                ->where('model', 'Order')
                ->where('name', 'Completed')
                ->value('id');
        } elseif ($order->status?->name === 'Completed') {
            $order->status_id = Status::query()
                ->where('model', 'Order')
                ->where('name', 'Confirmed')
                ->value('id');
        }
        $order->save();

        return $invoice->refresh();
    }

    public function settle(Event $model, array $payload): Event
    {
        return DB::transaction(function () use ($model, $payload) {
            $event = Event::query()
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $completedStatusId = Status::query()
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');

            if (!$completedStatusId) {
                throw new \RuntimeException('Completed payment status is not configured.');
            }

            $orders = $event->orders()
                ->with(['invoice.payments' => function ($query) use ($completedStatusId) {
                    $query->where('status_id', $completedStatusId);
                }])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($orders->isEmpty()) {
                throw ValidationException::withMessages([
                    'amount' => 'No order is available for this event.',
                ]);
            }

            $totalOutstanding = 0;
            foreach ($orders as $order) {
                $invoiceDiscount = (float) ($order->invoice?->discount ?? 0);
                $invoicePaid = (float) ($order->invoice?->payments?->sum('amount') ?? 0);
                $payable = max(
                    0,
                    (float) $order->grand_total
                    - (float) $order->discount
                    - $invoiceDiscount
                );
                $totalOutstanding += max(0, $payable - $invoicePaid);
            }

            $amount = round((float) ($payload['amount'] ?? 0), 2);
            $discount = round((float) ($payload['discount'] ?? 0), 2);
            $transactionTotal = round($amount + $discount, 2);

            if ($amount < 0 || $discount < 0 || $transactionTotal <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment and discount must be non-negative, and their total must be greater than zero.',
                ]);
            }

            if ($transactionTotal > round($totalOutstanding, 2)) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment and discount exceed the outstanding balance.',
                ]);
            }

            $remainingAmount = $amount;
            $remainingDiscount = $discount;

            foreach ($orders as $order) {
                $invoice = $order->invoice;
                if (!$invoice) {
                    $invoice = new Invoice();
                    $invoice->event_id = $event->id;
                    $invoice->order_id = $order->id;
                    $invoice->issued_at = today();
                    $invoice->due_at = today();
                    $invoice->save();
                }

                $invoicePaid = (float) $invoice->payments->sum('amount');
                $currentPayable = max(
                    0,
                    (float) $order->grand_total
                    - (float) $order->discount
                    - (float) $invoice->discount
                );
                $currentOutstanding = max(0, $currentPayable - $invoicePaid);

                if ($currentOutstanding <= 0) {
                    continue;
                }

                $discountPart = min($remainingDiscount, $currentOutstanding);
                $remainingDiscount = round($remainingDiscount - $discountPart, 2);
                $invoicePayable = round($currentPayable - $discountPart, 2);
                $amountPart = min(
                    $remainingAmount,
                    max(0, $invoicePayable - $invoicePaid)
                );
                $remainingAmount = round($remainingAmount - $amountPart, 2);

                if ($discountPart > 0 || $amountPart > 0) {
                    $invoice->discount = round((float) $invoice->discount + $discountPart, 2);

                    $payment = new Payment();
                    $payment->status_id = $completedStatusId;
                    $payment->invoice_id = $invoice->id;
                    $payment->payment_method_id = $payload['payment_method_id'];
                    $payment->discount = $discountPart;
                    $payment->amount = $amountPart;
                    $payment->paid_at = $payload['paid_at'] ?? now();
                    $payment->note = $payload['payment_note'] ?? null;
                    $payment->save();
                }

                $invoiceOutstanding = max(
                    0,
                    round($invoicePayable - $invoicePaid - $amountPart, 2)
                );
                $invoiceStatusName = $invoiceOutstanding <= 0
                    ? 'Paid'
                    : ($invoicePaid + $amountPart > 0 ? 'Partial' : 'Pending');

                $invoice->payable_amount = $invoicePayable;
                $invoice->outstanding_amount = $invoiceOutstanding;
                $invoice->status_id = Status::query()
                    ->where('model', 'Invoice')
                    ->where('name', $invoiceStatusName)
                    ->value('id');
                $invoice->save();

                $order->payable_amount = $invoiceOutstanding;
                if ($invoiceStatusName === 'Paid') {
                    $order->status_id = Status::query()
                        ->where('model', 'Order')
                        ->where('name', 'Completed')
                        ->value('id');
                }
                $order->save();
            }

            if ($remainingAmount > 0 || $remainingDiscount > 0) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment could not be fully allocated to the event orders.',
                ]);
            }

            $hasOutstanding = $event->orders()->where('payable_amount', '>', 0)->exists();
            if (!$hasOutstanding) {
                $event->statusLogs()->create([
                    'status_id' => Status::query()
                        ->where('model', 'Event')
                        ->where('name', 'Completed')
                        ->value('id'),
                ]);
            }

            return $event->refresh();
        }, 3);
    }
}
