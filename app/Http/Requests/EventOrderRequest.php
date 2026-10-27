<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Package\Models\Package;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\Service\Models\Service;

class EventOrderRequest extends FormRequest
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
            'package_id' => ['nullable', 'exists:packages,id'],

            'items' => ['nullable', 'array'],
            'items.*.inventory_category_id' => ['nullable'],
            'items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],

            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['nullable', 'exists:services,id'],
            'services.*.quantity' => ['nullable', 'numeric', 'min:1'],
            'services.*.unit_price' => ['nullable', 'numeric', 'min:0'],

            'subtotal' => ['nullable', 'numeric', 'min:0'],

            'payment.payment_method_id' => ['nullable'],
            'payment.discount' => ['nullable', 'numeric', 'min:0'],
            'payment.security_deposit' => ['nullable', 'numeric', 'min:0'],
            'payment.advance_amount' => ['nullable', 'numeric', 'min:0'],

            'event.note' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('package_id')) {
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
