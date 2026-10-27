<?php

namespace App\Modules\Event\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\Package\Models\Package;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\Service\Models\Service;

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

            // Event Information
            'event.event_category_id' => ['required', 'exists:event_categories,id'],
            'event.start_date' => ['required', 'date'],
            'event.end_date' => ['required', 'date', 'after:event.start_date'],
            'event.venue' => ['required', 'string', 'max:255'],
            'event.note' => ['nullable'],

            //package
            'package_id' => ['nullable', 'exists:packages,id'],
            'status_id' => ['nullable', 'exists:statuses,id'],

            // Inventory Items
            'items' => ['nullable', 'array'],
            'items.*.inventory_category_id' => ['nullable', 'exists:inventory_categories,id'],
            'items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],

            // Services
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['nullable', 'exists:services,id'],
            'services.*.quantity' => ['nullable', 'numeric', 'gt:0'],
            'services.*.unit_price' => ['nullable', 'numeric', 'min:0'],

            // Payment Information
            'payment.payment_method_id' => ['nullable', 'exists:payment_methods,id'],
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

        if ($this->has('event.event_category_id')) {
            $this->merge([
                'event' => array_merge($this->input('event', []), [
                    'event_category_id' => EventCategory::where(
                        'ulid',
                        $this->input('event.event_category_id')
                    )->value('id'),
                ]),
            ]);
        }

        if ($this->has('package_id') && $this->input('package_id')) {
            $this->merge([
                'package_id' => Package::where('ulid', $this->input('package_id'))->value('id')
            ]);
        }

        if ($this->has('payment.payment_method_id')) {
            $this->merge([
                'payment' => array_merge($this->input('payment', []), [
                    'payment_method_id' => PaymentMethod::where(
                        'ulid',
                        $this->input('payment.payment_method_id')
                    )->value('id'),
                ]),
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

                if (!empty($item['inventory_category_id'])) {
                    $items[$key]['inventory_category_id'] = InventoryCategory::where(
                            'ulid',
                            $item['inventory_category_id']
                        )->value('id') ?? $item['inventory_category_id'];
                }
            }

            $this->merge([
                'items' => $items,
            ]);
        }

        if ($this->has('services')) {
            $services = $this->input('services', []);

            foreach ($services as $key => $service) {
                if (!empty($service['service_id'])) {
                    $services[$key]['service_id'] = Service::where(
                        'ulid',
                        $service['service_id']
                    )->value('id');
                }
            }

            $this->merge([
                'services' => $services,
            ]);
        }
    }
}
