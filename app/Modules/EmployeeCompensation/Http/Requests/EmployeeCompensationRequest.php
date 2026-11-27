<?php

namespace App\Modules\EmployeeCompensation\Http\Requests;

use App\Enum\CommissionBasisEnum;
use App\Enum\CommissionTypeEnum;
use App\Models\Status;
use App\Modules\Designation\Models\Designation;
use App\Modules\Employee\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'employee_id' => ['required', 'exists:employees,id'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'pay_type' => ['required', Rule::in(['monthly', 'daily', 'hourly'])],
            'basic_pay' => ['nullable', 'numeric', 'min:0', 'required_if:pay_type,monthly,hybrid'],
            'daily_rate' => ['nullable', 'numeric', 'min:0', 'required_if:pay_type,daily'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'required_if:pay_type,hourly'],
            'commission_basis' => ['nullable', new Enum(CommissionBasisEnum::class)],
            'commission_type' => ['nullable', new Enum(CommissionTypeEnum::class)],
            'commission_rate' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'status_id' => Status::class,
            'employee_id' => Employee::class,
            'designation_id' => Designation::class,
        ] as $field => $modelClass) {
            if ($this->filled($field) && !ctype_digit((string) $this->input($field))) {
                $this->merge([
                    $field => $modelClass::query()->where('ulid', $this->input($field))->value('id'),
                ]);
            }
        }
    }
}
