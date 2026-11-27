<?php

namespace App\Modules\Payment\Repositories\Eloquent;

use App\Modules\Event\Models\Event;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Repositories\Contracts\PaymentContract;
use App\Modules\Payment\Services\PaymentSettlementService;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $event = Event::query()->findOrFail($payload['event_id']);

        return app(PaymentSettlementService::class)->settle($event, $payload);
    }

    public function updateModel(Model $model, array $payload): Model
    {
        return DB::transaction(function () use ($model, $payload) {
            $payment = Payment::query()
                ->with('invoice')
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = $payment->invoice;
            if ((string) $invoice->event_id !== (string) $payload['event_id']) {
                throw ValidationException::withMessages([
                    'event_id' => 'Payment cannot be moved to another event.',
                ]);
            }

            $order = Order::query()->whereKey($invoice->order_id)->firstOrFail();
            $completedStatusId = \App\Models\Status::query()
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');
            $otherPayments = $invoice->payments()
                ->where('status_id', $completedStatusId)
                ->whereKeyNot($payment->getKey());
            $otherPaid = (float) (clone $otherPayments)->sum('amount');
            $newAmount = round((float) $payload['amount'], 2);
            $newDiscount = round((float) ($payload['discount'] ?? 0), 2);
            $newInvoiceDiscount = max(
                0,
                (float) $invoice->discount
                - (float) $payment->discount
                + $newDiscount
            );
            $availablePayable = max(
                0,
                (float) $order->grand_total
                - (float) $order->discount
                - $newInvoiceDiscount
            );

            if ($otherPaid + $newAmount > $availablePayable) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment and discount exceed the order payable amount.',
                ]);
            }

            $payment->payment_method_id = $payload['payment_method_id'];
            $payment->amount = $newAmount;
            $payment->discount = $newDiscount;
            $payment->paid_at = $payload['paid_at'];
            $payment->note = $payload['payment_note'] ?? null;
            $payment->save();

            $invoice->discount = $newInvoiceDiscount;
            $invoice->save();

            app(PaymentSettlementService::class)->refreshInvoice($invoice);

            return $payment->refresh();
        });
    }

    public function softDeleteModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payment = Payment::query()
                ->with('invoice')
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $invoice = $payment->invoice;
            $deleted = (bool) $payment->delete();

            if ($deleted) {
                $invoice->discount = max(
                    0,
                    (float) $invoice->discount - (float) $payment->discount
                );
                $invoice->save();
                app(PaymentSettlementService::class)->refreshInvoice($invoice);
            }

            return $deleted;
        });
    }

    public function restoreModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payment = Payment::withTrashed()
                ->with('invoice')
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $invoice = $payment->invoice;
            $order = Order::query()->whereKey($invoice->order_id)->firstOrFail();
            $completedStatusId = \App\Models\Status::query()
                ->where('model', 'Payment')
                ->where('name', 'Completed')
                ->value('id');
            $currentPaid = (float) $invoice->payments()
                ->where('status_id', $completedStatusId)
                ->sum('amount');
            $restoredPayable = max(
                0,
                (float) $order->grand_total
                - (float) $order->discount
                - (float) $invoice->discount
                - (float) $payment->discount
            );
            if ($currentPaid + (float) $payment->amount > $restoredPayable) {
                throw ValidationException::withMessages([
                    'payment' => 'Restoring this payment would exceed the order payable amount.',
                ]);
            }
            $restored = (bool) $payment->restore();

            if ($restored) {
                $invoice->discount = round(
                    (float) $invoice->discount + (float) $payment->discount,
                    2
                );
                $invoice->save();
                app(PaymentSettlementService::class)->refreshInvoice($invoice);
            }

            return $restored;
        });
    }

    public function permanentlyDeleteModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payment = Payment::withTrashed()
                ->with('invoice')
                ->whereKey($model->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $invoice = $payment->invoice;
            $wasDeleted = $payment->trashed();
            $deleted = (bool) $payment->forceDelete();

            if ($deleted) {
                if (!$wasDeleted) {
                    $invoice->discount = max(
                        0,
                        (float) $invoice->discount - (float) $payment->discount
                    );
                    $invoice->save();
                }
                app(PaymentSettlementService::class)->refreshInvoice($invoice);
            }

            return $deleted;
        });
    }
}
