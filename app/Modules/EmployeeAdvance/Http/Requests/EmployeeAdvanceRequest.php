<?php

namespace App\Modules\EmployeeAdvance\Http\Requests;

use App\Models\Status;
use App\Models\User;
use App\Modules\Employee\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeAdvanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approved_by' => ['required', 'exists:users,id'],
            'status_id' => ['required', 'exists:statuses,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'advance_type' => ['required', 'string', 100],
            'amount' => ['required', 'numeric', 'gt:0'],
            'installment_amount' => ['nullable', 'numeric','min:0'],
            'advance_date' => ['required'],
            'repayment_start_date' => ['nullable'],
            'reason' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->filled('approved_by') && !is_numeric($this->input('approved_by'))) {
            $this->merge([
                'approved_by' => User::where('ulid', $this->input('approved_by'))->value('id')
            ]);
        }
        if ($this->filled('status_id') && !is_numeric($this->input('status_id'))) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
        if ($this->filled('employee_id') && !is_numeric($this->input('employee_id'))) {
            $this->merge([
                'employee_id' => Employee::where('ulid', $this->input('employee_id'))->value('id')
            ]);
        }
    }
}
