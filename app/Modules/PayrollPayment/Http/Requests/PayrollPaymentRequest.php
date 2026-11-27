<?php

namespace App\Modules\PayrollPayment\Http\Requests;

use App\Models\Status;
use App\Modules\Payroll\Models\Payroll;
use App\Modules\PaymentMethod\Models\PaymentMethod;
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
            'status_id' => ['nullable', 'exists:statuses,id'],
            'payroll_id' => ['required', 'exists:payrolls,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->filled('status_id') && !ctype_digit((string) $this->input('status_id'))) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->filled('payroll_id') && !ctype_digit((string) $this->input('payroll_id'))) {
            $this->merge([
                'payroll_id' => Payroll::where('ulid', $this->input('payroll_id'))->value('id')
            ]);
        }
        if ($this->filled('payment_method_id') && !ctype_digit((string) $this->input('payment_method_id'))) {
            $this->merge([
                'payment_method_id' => PaymentMethod::where('ulid', $this->input('payment_method_id'))->value('id')
            ]);
        }
    }
}
