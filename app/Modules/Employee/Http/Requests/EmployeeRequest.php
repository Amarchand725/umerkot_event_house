<?php

namespace App\Modules\Employee\Http\Requests;

use App\Models\Status;
use App\Modules\Employee\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'payable_units' => ['nullable', 'string', 100],
            'worked_hours' => ['nullable', 'numeric', 'min:0'],
            'overtime_hours' => ['nullable', 'numeric', 'min:0'],
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
