<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;

    class OrderReturnRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

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
                    'string',
                    'exists:orders,id',
                ],

                'items.*.order_item_id' => [
                    'required',
                    'string',
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

                'items.*.inspection_notes' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                // The form submits status_id, not inspection_status.
                'status_id' => [
                    'required',
                    'string',
                    'exists:statuses,ulid',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                // Settlement values are calculated on the server.
                // 'security_deposit_received' => [
                //     'prohibited',
                // ],

                // 'damage_deduction' => [
                //     'prohibited',
                // ],

                // 'missing_deduction' => [
                //     'prohibited',
                // ],

                // 'total_deduction' => [
                //     'prohibited',
                // ],

                // 'refundable_deposit' => [
                //     'prohibited',
                // ],

                // 'additional_amount_due' => [
                //     'prohibited',
                // ],
            ];
        }

        public function withValidator($validator): void
        {
            $validator->after(function ($validator) {
                foreach ($this->input('items', []) as $key => $item) {
                    if (
                        !isset(
                            $item['good_quantity'],
                            $item['damaged_quantity'],
                            $item['missing_quantity'],
                            $item['issued_quantity']
                        )
                    ) {
                        continue;
                    }

                    $good = (int) $item['good_quantity'];
                    $damaged = (int) $item['damaged_quantity'];
                    $missing = (int) $item['missing_quantity'];
                    $issued = (int) $item['issued_quantity'];

                    if (($good + $damaged + $missing) !== $issued) {
                        $validator->errors()->add(
                            "items.$key.good_quantity",
                            'Good, damaged, and missing quantities must equal the issued quantity.'
                        );
                    }
                }
            });
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

                'status_id.required' =>
                    'Please select an inspection status.',

                'status_id.exists' =>
                    'Please select a valid inspection status.',

                '*.prohibited' =>
                    'Settlement amounts must be calculated by the server.',
            ];
        }
    }
