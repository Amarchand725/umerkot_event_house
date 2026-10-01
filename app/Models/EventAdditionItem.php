<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAdditionItem extends Model
{
    use SoftDeletes, ModelTrait;

    protected $fillable = [
        'event_addition_id', 'inventory_item_id', 'quantity', 'unit_price', 'start_date', 'end_date', 'total', 'note'
    ];
}
