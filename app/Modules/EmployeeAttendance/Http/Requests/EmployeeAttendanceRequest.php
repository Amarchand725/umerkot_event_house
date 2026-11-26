<?php

namespace App\Modules\EmployeeAttendance\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Employee\Models\Employee;

class EmployeeAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'payable_units' => ['required', 'numeric', 'in:0,0.5,1'],
            'worked_hours' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'overtime_hours' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'notes' => ['nullable', 'string', 'max:255'],
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
