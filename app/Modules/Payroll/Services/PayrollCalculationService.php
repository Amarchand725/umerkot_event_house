<?php

namespace App\Modules\Payroll\Services;

use App\Models\EmployeeAdvanceRepayment;
use App\Models\PayrollItem;
use App\Models\Status;
use App\Modules\Employee\Models\Employee;
use App\Modules\EmployeeAdvance\Models\EmployeeAdvance;
use App\Modules\EmployeeAttendance\Models\EmployeeAttendance;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;
use App\Modules\Payroll\Models\Payroll;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollCalculationService
{
    public function create(array $payload): Payroll
    {
        return DB::transaction(function () use ($payload) {
            $employee = Employee::query()
                ->whereKey($payload['employee_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (Payroll::query()
                ->where('employee_id', $employee->id)
                ->where('month', $payload['month'])
                ->where('year', $payload['year'])
                ->exists()) {
                throw ValidationException::withMessages([
                    'month' => 'A payroll already exists for this employee and period.',
                ]);
            }

            $calculation = $this->calculate($employee, $payload);
            $payroll = new Payroll();
            $payroll->fill($calculation['payroll']);
            $payroll->author_id = auth()->id();
            $payroll->save();

            $this->saveItemsAndRepayments($payroll, $calculation);

            return $payroll->refresh();
        }, 3);
    }

    public function update(Payroll $payroll, array $payload): Payroll
    {
        return DB::transaction(function () use ($payroll, $payload) {
            $payroll = Payroll::query()
                ->whereKey($payroll->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $draftStatusId = $this->statusId('Payroll', 'draft');
            if ((int) $payroll->status_id !== (int) $draftStatusId) {
                throw ValidationException::withMessages([
                    'payroll' => 'Only draft payrolls can be recalculated.',
                ]);
            }

            if ($payroll->payments()->exists()) {
                throw ValidationException::withMessages([
                    'payroll' => 'A payroll with payments cannot be recalculated.',
                ]);
            }

            $duplicate = Payroll::query()
                ->where('employee_id', $payload['employee_id'])
                ->where('month', $payload['month'])
                ->where('year', $payload['year'])
                ->whereKeyNot($payroll->getKey())
                ->exists();
            if ($duplicate) {
                throw ValidationException::withMessages([
                    'month' => 'A payroll already exists for this employee and period.',
                ]);
            }

            $this->removeDraftRepayments($payroll);
            PayrollItem::query()->where('payroll_id', $payroll->id)->delete();

            $employee = Employee::query()
                ->whereKey($payload['employee_id'])
                ->lockForUpdate()
                ->firstOrFail();
            $calculation = $this->calculate($employee, $payload);
            $payroll->fill($calculation['payroll']);
            $payroll->save();

            $this->saveItemsAndRepayments($payroll, $calculation);

            return $payroll->refresh();
        }, 3);
    }

    private function calculate(Employee $employee, array $payload): array
    {
        $compensation = EmployeeCompensation::query()
            ->whereKey($payload['employee_compensation_id'])
            ->where('employee_id', $employee->id)
            ->where('status_id', $this->statusId('EmployeeCompensation', 'active'))
            ->whereDate('effective_from', '<=', $payload['period_end'])
            ->where(function ($query) use ($payload) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $payload['period_start']);
            })
            ->lockForUpdate()
            ->first();

        if (!$compensation) {
            throw ValidationException::withMessages([
                'employee_compensation_id' => 'Select an active compensation record effective during the payroll period.',
            ]);
        }

        $attendance = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date_time', [$payload['period_start'], $payload['period_end'].' 23:59:59'])
            ->get(['payable_units', 'worked_hours', 'overtime_hours']);

        $payableUnits = round((float) $attendance->sum('payable_units'), 2);
        $workingHours = round((float) $attendance->sum('worked_hours'), 2);
        $overtimeHours = round((float) $attendance->sum('overtime_hours'), 2);

        $payType = strtolower((string) $compensation->pay_type);
        $grossEarnings = match ($payType) {
            'monthly' => (float) $compensation->basic_pay,
            'daily' => (float) $compensation->daily_rate * $payableUnits,
            'hourly' => (float) $compensation->hourly_rate * ($workingHours + $overtimeHours),
            default => throw ValidationException::withMessages([
                'employee_compensation_id' => 'Payroll is currently supported for monthly, daily, and hourly pay types only.',
            ]),
        };
        $grossEarnings = round(max(0, $grossEarnings), 2);

        $advances = $this->dueAdvances($employee, $payload, $grossEarnings);
        $totalDeductions = round(array_sum(array_column($advances, 'amount')), 2);
        $netPayable = max(0, round($grossEarnings - $totalDeductions, 2));
        $draftStatusId = $this->statusId('Payroll', 'draft');

        if (!$draftStatusId) {
            throw new \RuntimeException('Draft payroll status is not configured.');
        }

        $snapshot = $compensation->only([
            'pay_type', 'effective_from', 'effective_to', 'basic_pay',
            'daily_rate', 'hourly_rate', 'commission_basis',
            'commission_type', 'commission_rate',
        ]);

        return [
            'payroll' => [
                'status_id' => $draftStatusId,
                'employee_id' => $employee->id,
                'employee_compensation_id' => $compensation->id,
                'month' => $payload['month'],
                'year' => $payload['year'],
                'period_start' => $payload['period_start'],
                'period_end' => $payload['period_end'],
                'compensation_snapshot' => $snapshot,
                'gross_earnings' => $grossEarnings,
                'total_deductions' => $totalDeductions,
                'net_payable' => $netPayable,
                'total_present_days' => $attendance->filter(
                    fn ($row) => (float) $row->payable_units > 0
                )->count(),
                'total_payable_days' => $payableUnits,
                'total_working_hours' => $workingHours,
                'total_overtime_hours' => $overtimeHours,
            ],
            'earning' => [
                'description' => ucfirst($payType).' earnings',
                'amount' => $grossEarnings,
                'source' => $compensation,
            ],
            'advances' => $advances,
        ];
    }

    private function dueAdvances(Employee $employee, array $payload, float $grossEarnings): array
    {
        if ($grossEarnings <= 0) {
            return [];
        }

        $disbursedStatusId = $this->statusId('EmployeeAdvance', 'disbursed');
        if (!$disbursedStatusId) {
            return [];
        }

        $advances = EmployeeAdvance::query()
            ->where('employee_id', $employee->id)
            ->where('status_id', $disbursedStatusId)
            ->whereNotNull('disbursed_at')
            ->where(function ($query) use ($payload) {
                $query->whereNull('repayment_start_date')
                    ->orWhereDate('repayment_start_date', '<=', $payload['period_end']);
            })
            ->orderBy('advance_date')
            ->lockForUpdate()
            ->get();

        $remainingForPayroll = $grossEarnings;
        $prepared = [];
        foreach ($advances as $advance) {
            $installment = (float) ($advance->installment_amount ?? 0);
            if ($installment <= 0 || $remainingForPayroll <= 0) {
                continue;
            }

            $repaid = (float) EmployeeAdvanceRepayment::query()
                ->where('employee_advance_id', $advance->id)
                ->sum('amount');
            $balance = max(0, round((float) $advance->amount - $repaid, 2));
            $deduction = min($installment, $balance, $remainingForPayroll);

            if ($deduction <= 0) {
                continue;
            }

            $prepared[] = [
                'advance' => $advance,
                'amount' => round($deduction, 2),
                'remaining_balance' => round($balance - $deduction, 2),
            ];
            $remainingForPayroll = round($remainingForPayroll - $deduction, 2);
        }

        return $prepared;
    }

    private function saveItemsAndRepayments(Payroll $payroll, array $calculation): void
    {
        PayrollItem::create([
            'payroll_id' => $payroll->id,
            'item_type' => 'earning',
            'description' => $calculation['earning']['description'],
            'amount' => $calculation['earning']['amount'],
            'source_type' => $calculation['earning']['source']->getMorphClass(),
            'source_id' => $calculation['earning']['source']->id,
        ]);

        foreach ($calculation['advances'] as $row) {
            $advance = $row['advance'];
            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'item_type' => 'deduction',
                'description' => 'Advance repayment',
                'amount' => $row['amount'],
                'source_type' => $advance->getMorphClass(),
                'source_id' => $advance->id,
            ]);

            EmployeeAdvanceRepayment::create([
                'payroll_id' => $payroll->id,
                'employee_advance_id' => $advance->id,
                'amount' => $row['amount'],
                'repayment_date' => $payroll->period_end,
                'notes' => 'Payroll installment repayment',
            ]);

            if ($row['remaining_balance'] <= 0) {
                $advance->status_id = $this->statusId('EmployeeAdvance', 'settled');
                $advance->save();
            }
        }
    }

    private function removeDraftRepayments(Payroll $payroll): void
    {
        $repayments = EmployeeAdvanceRepayment::query()
            ->where('payroll_id', $payroll->id)
            ->get();

        foreach ($repayments as $repayment) {
            $advance = EmployeeAdvance::query()
                ->whereKey($repayment->employee_advance_id)
                ->lockForUpdate()
                ->first();
            $repayment->delete();

            if ($advance) {
                $repaid = (float) EmployeeAdvanceRepayment::query()
                    ->where('employee_advance_id', $advance->id)
                    ->sum('amount');
                $advance->status_id = $repaid >= (float) $advance->amount
                    ? $this->statusId('EmployeeAdvance', 'settled')
                    : $this->statusId('EmployeeAdvance', 'disbursed');
                $advance->save();
            }
        }
    }

    private function statusId(string $model, string $name): ?int
    {
        return Status::query()
            ->where('model', $model)
            ->where('name', $name)
            ->value('id');
    }
}
