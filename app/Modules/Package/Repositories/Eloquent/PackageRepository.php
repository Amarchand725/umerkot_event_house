<?php

namespace App\Modules\Package\Repositories\Eloquent;

use App\Models\Status;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Package\Repositories\Contracts\PackageContract;
use App\Modules\Package\Models\Package;

class PackageRepository extends BaseRepository implements PackageContract
{
    protected $status = null;
    
    public function __construct(Package $model)
    {
        parent::__construct($model);

        $this->status = Status::query();
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model;
        $model->toFill($payload, ['items', 'services']);
        $model->save();

        foreach ($payload['items'] ?? [] as $item) {
            $model->inventoryItems()->create([
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity'          => $item['quantity'],
                'unit_price'        => (float) $item['unit_price'],
                'subtotal'          => (float) $item['quantity'] * (float) $item['unit_price'],
            ]);
        }

        foreach ($payload['services'] ?? [] as $service) {
            $model->inventoryItems()->create([
                'service_id' => $service['service_id'],
                'quantity'   => $service['quantity'],
                'unit_price' => (float) $service['unit_price'],
                'subtotal'   => (float) $service['quantity'] * (float) $service['unit_price'],
            ]);
        }

        $model->price = $model->inventoryItems()->sum('subtotal');
        $model->save();

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // Update Package Information
        $model->toFill($payload, ['items', 'services']);
        $model->save();

        // Delete Existing Items and Services
        $model->inventoryItems()->delete();

        // Store Inventory Items
        foreach ($payload['items'] ?? [] as $item) {

            $model->inventoryItems()->create([
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity'          => $item['quantity'],
                'unit_price'        => (float) $item['unit_price'],
                'subtotal'          => (float) $item['quantity'] * (float) $item['unit_price'],
            ]);
        }

        // Store Services
        foreach ($payload['services'] ?? [] as $service) {

            $model->inventoryItems()->create([
                'service_id' => $service['service_id'],
                'quantity'   => $service['quantity'],
                'unit_price' => (float) $service['unit_price'],
                'subtotal'   => (float) $service['quantity'] * (float) $service['unit_price'],
            ]);
        }

        // Recalculate Original Package Price
        $model->price = $model->inventoryItems()->sum('subtotal');
        $model->save();

        return $model->refresh();
    }
}