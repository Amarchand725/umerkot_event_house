<?php

namespace App\Modules\Payroll\Http\Requests;

use App\Modules\Employee\Models\Employee;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class PayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['prohibited'],
            'employee_id' => ['required', 'exists:employees,id'],
            'employee_compensation_id' => ['required', 'exists:employee_compensation,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'compensation_snapshot' => ['prohibited'],
            'gross_earnings' => ['prohibited'],
            'total_deductions' => ['prohibited'],
            'net_payable' => ['prohibited'],
            'total_present_days' => ['prohibited'],
            'total_payable_days' => ['prohibited'],
            'total_working_hours' => ['prohibited'],
            'total_overtime_hours' => ['prohibited'],
            'approved_at' => ['prohibited'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->filled('employee_id') && !ctype_digit((string) $this->input('employee_id'))) {
            $this->merge([
                'employee_id' => Employee::where('ulid', $this->input('employee_id'))->value('id')
            ]);
        }

        if ($this->filled('employee_compensation_id') && !ctype_digit((string) $this->input('employee_compensation_id'))) {
            $this->merge([
                'employee_compensation_id' => EmployeeCompensation::where(
                    'ulid',
                    $this->input('employee_compensation_id')
                )->value('id'),
            ]);
        }

        if (
            is_numeric($this->input('month')) &&
            is_numeric($this->input('year')) &&
            (int) $this->input('month') >= 1 &&
            (int) $this->input('month') <= 12
        ) {
            $period = Carbon::create((int) $this->input('year'), (int) $this->input('month'), 1);
            $this->merge([
                'period_start' => $period->copy()->startOfMonth()->toDateString(),
                'period_end' => $period->copy()->endOfMonth()->toDateString(),
            ]);
        }
    }
}
