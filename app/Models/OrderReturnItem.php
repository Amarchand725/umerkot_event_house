<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderReturnItem extends Model
{
    use ModelTrait, HasFactory;

    protected $fillable = [
        'order_return_id', 'order_id', 'order_item_id', 'issued_quantity',
        'good_quantity', 'damaged_quantity', 'missing_quantity', 'damage_charge',
        'replacement_charge', 'deduction_amount', 'inspection_notes'
    ];
}
