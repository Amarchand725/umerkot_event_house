<?php

namespace App\Modules\Event\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\Package\Models\Package;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\InventoryItem\Models\InventoryItem;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer Information
            'name' => ['required', 'string', 'max:255'],
            'caste' => ['required', 'string', 'max:255'],
            'cnic_no' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'alter_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],

            // Event Information
            'status_id' => ['nullable', 'exists:statuses,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'event_category_id' => ['required', 'exists:event_categories,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'venue' => ['required', 'string', 'max:255'],
            'note' => ['nullable'],

            // Inventory Items
            'items' => ['nullable', 'array'],
            'items.*.inventory_category_id' => ['nullable', 'exists:inventory_categories,id'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],

            // Services
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'numeric', 'gt:0'],
            'services.*.unit_price' => ['required', 'numeric', 'min:0'],

            // Payment Information
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'advance_amount' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('event_category_id')) {
            $this->merge([
                'event_category_id' => EventCategory::where('ulid', $this->input('event_category_id'))->value('id')
            ]);
        }

        if ($this->has('package_id') && $this->input('package_id')) {
            $this->merge([
                'package_id' => Package::where('ulid', $this->input('package_id'))->value('id')
            ]);
        }

        if ($this->has('payment_method_id')) {
            $this->merge([
                'payment_method_id' => PaymentMethod::where('ulid', $this->input('payment_method_id'))->value('id')
            ]);
        }

        if ($this->has('items')) {
            $items = $this->input('items', []);

            foreach ($items as $key => $item) {
                if (!empty($item['inventory_item_id'])) {
                    $items[$key]['inventory_item_id'] = InventoryItem::where(
                        'ulid',
                        $item['inventory_item_id']
                    )->value('id');
                }
            }

            $this->merge([
                'items' => $items,
            ]);
        }
    }
}
