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
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.caste' => ['required', 'string', 'max:255'],
            'customer.cnic_no' => ['required', 'string', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:20'],
            'customer.alter_phone' => ['nullable', 'string', 'max:20'],
            'customer.address' => ['required', 'string', 'max:255'],
            
            //package
            'package_id' => ['nullable', 'exists:packages,id'],

            // Event Information
            'event.status_id' => ['nullable', 'exists:statuses,id'],
            'event.event_category_id' => ['required', 'exists:event_categories,id'],
            'event.start_date' => ['required', 'date'],
            'event.end_date' => ['required', 'date', 'after:event.start_date'],
            'event.venue' => ['required', 'string', 'max:255'],
            'event.note' => ['nullable'],

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
            'payment.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payment.subtotal' => ['nullable', 'numeric', 'min:0'],
            'payment.discount' => ['nullable', 'numeric', 'min:0'],
            'payment.security_deposit' => ['nullable', 'numeric', 'min:0'],
            'payment.advance_amount' => ['nullable', 'numeric', 'min:0'],
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
