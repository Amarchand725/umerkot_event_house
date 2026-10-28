<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\OrderReturn\Models\OrderReturn;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnItem extends Model
{
    use ModelTrait, HasFactory;

    protected $fillable = [
        'order_return_id', 'order_id', 'order_item_id', 'issued_quantity',
        'good_quantity', 'damaged_quantity', 'missing_quantity', 'damage_charge',
        'replacement_charge', 'deduction_amount', 'inspection_notes'
    ];

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
