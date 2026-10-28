<?php

namespace App\Models;

use App\Models\Traits\ModelTrait;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Order\Models\Order;
use App\Modules\Package\Models\Package;
use App\Modules\Service\Models\Service;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use ModelTrait;

    protected $fillable = [
        'order_id', 'inventory_item_id', 'service_id', 'package_id', 'quantity', 'unit_price', 'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'id', 'inventory_item_id');
    }

    public function item(){
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'id', 'service_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
