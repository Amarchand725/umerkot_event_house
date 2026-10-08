<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Package\Models\Package;
use App\Modules\Service\Models\Service;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageItem extends Model
{
    use SoftDeletes, ModelTrait;

    protected $fillable = ['package_id', 'inventory_item_id', 'quantity', 'unit_price'];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
