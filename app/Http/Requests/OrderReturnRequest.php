<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderReturnRequest extends FormRequest
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
            'returned_at' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*' => [
                'required',
                'array',
            ],

            'items.*.order_id' => [
                'required',
                'uuid',
                'exists:orders,id',
            ],

            'items.*.order_item_id' => [
                'required',
                'uuid',
                'distinct',
                'exists:order_items,id',
            ],

            'items.*.issued_quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.good_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'items.*.damaged_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'items.*.missing_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'items.*.condition' => [
                'required',
                Rule::in([
                    'good',
                    'damaged',
                    'unusable',
                    'mixed',
                ]),
            ],

            'items.*.inspection_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'inspection_status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'returned_at.required' =>
                'The return date is required.',

            'items.required' =>
                'At least one return item is required.',

            'items.*.order_id.exists' =>
                'The selected order does not exist.',

            'items.*.order_item_id.distinct' =>
                'An order item cannot be submitted more than once.',

            'items.*.good_quantity.required' =>
                'The good quantity is required.',

            'items.*.damaged_quantity.required' =>
                'The damaged quantity is required.',

            'items.*.missing_quantity.required' =>
                'The missing quantity is required.',

            'items.*.condition.in' =>
                'Please select a valid item condition.',

            'inspection_status.in' =>
                'Please select a valid inspection status.',
        ];
    }
}
