<?php

namespace App\Modules\Payment\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Payment\Repositories\Contracts\PaymentContract;
use App\Models\Status;
use App\Modules\Payment\Models\Payment;
use App\Modules\Event\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    protected $eventModel;
    protected $status = null;

    public function __construct(Payment $model)
    {
        parent::__construct($model);

        $this->eventModel = new Event();
        $this->status = Status::query();
    }

    public function storeModel(array $payload): Model
    {
        return DB::transaction(function () use ($payload) {
            $model = $this->eventModel::where('id', $payload['event_id'])->first();

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
                    $invoice->discount = $invoice->discount+$discount;
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
                $payment->discount = $discount;
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
                $order->payable_amount = $order->payable_amount+$discount;
                if($invoiceStatusName=='Paid'){
                    $order->status_id = (clone $this->status)
                        ->where('model', 'Order')
                        ->where('name', 'Completed')
                        ->value('id');

                    $logStatus['status_id'] = (clone $this->status)
                                    ->where('model', 'Event')
                                    ->where('name', 'Completed')
                                    ->value('id');

                    $logStatus['model_id'] = $event->id;
                    $logStatus['model_type'] = $event->getMorphClass();

                    $log = $event->statusLogs()->firstOrNew();
                    $log->toFill($logStatus);
                    $log->save();
                }
                $order->save();
            }

            return $event->refresh();
        }, 3);
    }
}
