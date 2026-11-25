<?php

namespace App\Modules\EmployeeCompensation\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeCompensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'effective_from' => ['required'],
            'pay_type' => ['required'],
            'basic_pay' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'commission_basis' => ['nullable', 'string', 'max:255'],
            'commission_type' => ['nullable', 'string', 'max:255'],
            'commission_rate' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
    }
}
