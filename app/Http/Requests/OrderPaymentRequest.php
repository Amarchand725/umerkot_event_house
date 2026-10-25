<?php

namespace App\Http\Requests;

use App\Modules\PaymentMethod\Models\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method_id' => [
                'required',
                'exists:payment_methods,id',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'paid_at' => [
                'required',
                'date',
            ],

            'payment_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('payment_method_id')) {

            $paymentMethodId = $this->input('payment_method_id');

            if (!ctype_digit((string) $paymentMethodId)) {
                $paymentMethodId = PaymentMethod::where(
                    'ulid',
                    $paymentMethodId
                )->value('id');
            }

            $this->merge([
                'payment_method_id' => $paymentMethodId,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'Please select a payment method.',
            'payment_method_id.exists' => 'Selected payment method is invalid.',
            'discount.numeric' => 'Discount must be a valid amount.',
            'discount.min' => 'Discount cannot be negative.',
            'amount.required' => 'Please enter the payment amount.',
            'amount.gt' => 'Payment amount must be greater than zero.',
            'paid_at.required' => 'Please select the payment date.',
        ];
    }
}
