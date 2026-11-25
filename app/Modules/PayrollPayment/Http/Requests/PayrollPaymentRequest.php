<?php

namespace App\Modules\PayrollPayment\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class PayrollPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['required', 'exists:statuses,id'],
            'payroll_id' => ['required', 'exists:payrolls,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['nullable'],
            'payment_date' => ['nullable'],
            'reference_number' => ['nullable'],
            'notes' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('payroll_id')) {
            $this->merge([
                'payroll_id' => Payroll::where('ulid', $this->input('payroll_id'))->value('id')
            ]);
        }
        if ($this->has('payment_method_id')) {
            $this->merge([
                'payment_method_id' => PaymentMethod::where('ulid', $this->input('payment_method_id'))->value('id')
            ]);
        }
    }
}
