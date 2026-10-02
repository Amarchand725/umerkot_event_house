<?php

namespace App\Modules\Expense\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'event_id' => ['nullable', 'exists:events,id'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'attachment_id' => ['nullable', 'exists:attachments,id'],

            'amount' => ['required', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('status_id')) {
            $data['status_id'] = Status::where(
                'ulid',
                $this->input('status_id')
            )->value('id');
        }

        if ($this->has('event_id')) {
            $data['event_id'] = Event::where(
                'ulid',
                $this->input('event_id')
            )->value('id');
        }

        if ($this->has('expense_category_id')) {
            $data['expense_category_id'] = ExpenseCategory::where(
                'ulid',
                $this->input('expense_category_id')
            )->value('id');
        }

        if ($this->has('payment_method_id')) {
            $data['payment_method_id'] = PaymentMethod::where(
                'ulid',
                $this->input('payment_method_id')
            )->value('id');
        }

        if ($this->has('attachment_id')) {
            $data['attachment_id'] = Attachment::where(
                'ulid',
                $this->input('attachment_id')
            )->value('id');
        }

        $this->merge($data);
    }
}
