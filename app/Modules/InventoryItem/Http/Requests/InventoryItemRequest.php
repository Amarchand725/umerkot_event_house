<?php

namespace App\Modules\InventoryItem\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class InventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'inventory_category_id' => ['nullable', 'exists:inventory_categories,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'total_quantity' => ['nullable', 'integer', 'max:255'],
            'minimum_quantity' => ['nullable', 'integer', 'max:255'],
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
