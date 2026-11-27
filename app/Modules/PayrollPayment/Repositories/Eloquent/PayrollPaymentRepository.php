<?php

namespace App\Modules\PayrollPayment\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\PayrollPayment\Repositories\Contracts\PayrollPaymentContract;
use App\Modules\PayrollPayment\Models\PayrollPayment;
use App\Modules\Payroll\Models\Payroll;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollPaymentRepository extends BaseRepository implements PayrollPaymentContract
{
    public function __construct(PayrollPayment $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $payroll = Payroll::query()->whereKey($data['payroll_id'])->lockForUpdate()->firstOrFail();
            if ($this->isActivePayment($data['status_id'] ?? null)) {
                $this->ensureWithinNetPay(
                    $payroll,
                    $this->paidAmountFor($payroll) + (float) $data['amount']
                );
            }

            $payment = parent::storeModel($data);
            $this->syncPayrollStatus($payroll);

            return $payment;
        }, 3);
    }

    public function updateModel(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            $payrollIds = array_unique([$model->payroll_id, $data['payroll_id']]);
            $payrolls = Payroll::query()->whereIn('id', $payrollIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $payroll = $payrolls->get($data['payroll_id']);
            if (!$payroll) {
                throw ValidationException::withMessages(['payroll_id' => 'The selected payroll is unavailable.']);
            }

            if ($this->isActivePayment($data['status_id'] ?? $model->status_id)) {
                $this->ensureWithinNetPay(
                    $payroll,
                    $this->paidAmountFor($payroll, (int) $model->getKey()) + (float) $data['amount']
                );
            }

            $updated = parent::updateModel($model, $data);
            foreach ($payrolls as $lockedPayroll) {
                $this->syncPayrollStatus($lockedPayroll);
            }

            return $updated;
        }, 3);
    }

    public function restoreModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payroll = Payroll::query()->whereKey($model->payroll_id)->lockForUpdate()->firstOrFail();
            if ($this->isActivePayment($model->status_id)) {
                $this->ensureWithinNetPay(
                    $payroll,
                    $this->paidAmountFor($payroll) + (float) $model->amount
                );
            }

            $restored = parent::restoreModel($model);
            $this->syncPayrollStatus($payroll);

            return $restored;
        }, 3);
    }

    public function softDeleteModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payroll = Payroll::query()->whereKey($model->payroll_id)->lockForUpdate()->firstOrFail();
            $deleted = parent::softDeleteModel($model);
            $this->syncPayrollStatus($payroll);

            return $deleted;
        }, 3);
    }

    public function permanentlyDeleteModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $payroll = Payroll::query()->whereKey($model->payroll_id)->lockForUpdate()->first();
            $deleted = parent::permanentlyDeleteModel($model);
            if ($payroll) {
                $this->syncPayrollStatus($payroll);
            }

            return $deleted;
        }, 3);
    }

    private function ensureWithinNetPay(Payroll $payroll, float $amount): void
    {
        if ($amount > (float) $payroll->net_payable) {
            throw ValidationException::withMessages([
                'amount' => 'Payroll payments cannot exceed the net payable amount.',
            ]);
        }
    }

    private function syncPayrollStatus(Payroll $payroll): void
    {
        $paid = $this->paidAmountFor($payroll);
        $net = (float) $payroll->net_payable;
        $statusName = $paid >= $net && $net > 0
            ? 'paid'
            : ($paid > 0 ? 'partially_paid' : null);

        if ($statusName === null) {
            if (in_array($payroll->status?->name, ['paid', 'partially_paid'], true)) {
                $statusName = 'approved';
            } else {
                return;
            }
        }

        $statusId = Status::query()
            ->where('model', 'Payroll')
            ->where('name', $statusName)
            ->value('id');
        if ($statusId) {
            $payroll->status_id = $statusId;
            $payroll->save();
        }
    }

    private function paidAmountFor(Payroll $payroll, ?int $exceptPaymentId = null): float
    {
        $query = PayrollPayment::query()->where('payroll_id', $payroll->id);
        $activeStatusId = Status::query()
            ->where('model', 'PayrollPayment')
            ->where('name', 'active')
            ->value('id');

        if ($activeStatusId) {
            $query->where(function ($activeQuery) use ($activeStatusId) {
                $activeQuery->where('status_id', $activeStatusId)
                    ->orWhereNull('status_id');
            });
        }
        if ($exceptPaymentId !== null) {
            $query->whereKeyNot($exceptPaymentId);
        }

        return (float) $query->sum('amount');
    }

    private function isActivePayment(mixed $statusId): bool
    {
        $activeStatusId = Status::query()
            ->where('model', 'PayrollPayment')
            ->where('name', 'active')
            ->value('id');

        return !$activeStatusId || !$statusId || (int) $statusId === (int) $activeStatusId;
    }
}
