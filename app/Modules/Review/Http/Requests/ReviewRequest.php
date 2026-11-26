<?php

namespace App\Modules\Review\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'rating' => ['nullable', 'numeric', 5],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
        if ($this->has('customer_id')) {
            $this->merge([
                'customer_id' => Customer::where('ulid', $this->input('customer_id'))->value('id')
            ]);
        }
    }
}
