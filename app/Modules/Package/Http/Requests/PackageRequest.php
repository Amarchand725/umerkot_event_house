<?php

namespace App\Modules\Package\Http\Requests;

use App\Models\Status;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Service\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id|distinct',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',

            'services' => 'nullable|array',
            'services.*.service_id' => 'required|exists:services,id|distinct',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if (empty($this->input('items')) && empty($this->input('services'))) {
                $validator->errors()->add(
                    'items',
                    'A package must contain at least one inventory item or service.'
                );
            }
        }];
    }

    public function prepareForValidation()
    {   
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('ulid', $this->input('status_id'))->value('id')
            ]);
        }
        if ($this->has('items')) {
            $items = collect($this->input('items', []))
                ->filter(fn ($item) => !empty($item['inventory_item_id']))
                ->values()
                ->all();

            foreach ($items as $key => $item) {
                if (!empty($item['inventory_item_id'])) {
                    if (!ctype_digit((string) $item['inventory_item_id'])) {
                        $items[$key]['inventory_item_id'] = InventoryItem::where(
                            'ulid',
                            $item['inventory_item_id']
                        )->value('id');
                    }
                }
            }

            $this->merge([
                'items' => $items,
            ]);
        }

        if ($this->has('services')) {
            $services = collect($this->input('services', []))
                ->filter(fn ($service) => !empty($service['service_id']))
                ->values()
                ->all();

            foreach ($services as $key => $service) {
                if (!empty($service['service_id'])) {
                    if (!ctype_digit((string) $service['service_id'])) {
                        $services[$key]['service_id'] = Service::where(
                            'ulid',
                            $service['service_id']
                        )->value('id');
                    }
                }
            }

            $this->merge([
                'services' => $services,
            ]);
        }
    }
}
