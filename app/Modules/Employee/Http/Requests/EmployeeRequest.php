<?php

namespace App\Modules\Employee\Http\Requests;

use App\Models\Status;
use App\Modules\Employee\Models\Employee;
use App\Modules\Designation\Models\Designation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

use App\Enum\CommissionTypeEnum;
use App\Enum\PayTypeEnum;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'compensation.designation_id' => ['nullable', 'exists:designations,id'],

            'joining_date' => ['nullable', 'date'],
            'name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'caste' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],

            'reference.full_name' => ['nullable', 'string', 'max:255'],
            'reference.phone' => ['nullable', 'string', 'max:30'],
            'reference.address' => ['nullable', 'string'],

            'compensation.effective_from' => ['nullable', 'date'],
            'compensation.pay_type' => ['nullable', new Enum(PayTypeEnum::class)],
            'compensation.basic_pay' => ['nullable', 'numeric', 'min:0'],
            'compensation.daily_rate' => ['nullable', 'numeric', 'min:0'],
            'compensation.hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'compensation.commission_basis' => ['nullable', 'string'],
            'compensation.commission_type' => ['nullable', new Enum(CommissionTypeEnum::class)],
            'compensation.commission_rate' => ['nullable', 'numeric', 'min:0'],
            'compensation.note' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('compensation.designation_id')) {
            $this->merge([
                'designation_id' => array_merge($this->input('designation_id', []), [
                    'designation_id' => Designation::where(
                        'ulid',
                        $this->input('compensation.designation_id')
                    )->value('id'),
                ]),
            ]);
        }
    }
}
