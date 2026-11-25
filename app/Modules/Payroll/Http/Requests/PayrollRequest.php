<?php

namespace App\Modules\Payroll\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['required', 'exists:statuses,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'employee_compensation_id' => ['required', 'exists:employee_compensation,id'],
            'month' => ['nullable'],
            'year' => ['nullable'],
            'period_start' => ['nullable'],
            'period_end' => ['nullable'],
            'compensation_snapshot' => ['nullable'],
            'gross_earnings' => ['nullable'],
            'total_deductions' => ['nullable'],
            'net_payable' => ['nullable'],
            'total_present_days' => ['nullable'],
            'total_payable_days' => ['nullable'],
            'total_working_hours' => ['nullable'],
            'total_overtime_hours' => ['nullable'],
            'approved_at' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('employee_id')) {
            $this->merge([
                'employee_id' => Employee::where('ulid', $this->input('employee_id'))->value('id')
            ]);
        }
    }
}
