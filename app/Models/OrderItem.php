<?php

namespace App\Models;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use ModelTrait;

    protected $fillable = [
        'order_id', 'inventory_item_id', 'service_id', 'package_id', 'quantity', 'unit_price', 'subtotal'
    ];
}
