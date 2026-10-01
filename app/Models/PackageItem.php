<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageItem extends Model
{
    use SoftDeletes, ModelTrait;

    protected $fillable = ['package_id', 'inventory_item_id', 'quantity', 'unit_price'];
}
