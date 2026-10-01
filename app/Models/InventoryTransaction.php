<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;

class InventoryTransaction extends Model
{
    use ModelTrait;

    protected $fillable = ['event_id', 'inventory_item_id', 'event_item_id', 'quantity', 'available_quantity', 'transaction_date', 'note'];
}
