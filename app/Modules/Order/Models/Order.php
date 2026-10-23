<?php

namespace App\Modules\Order\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Services\DocumentNumberService;

class Order extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = [
        'order_number', 'status_id', 'event_id', 'grand_total', 'discount', 'payable_amount', 'security_deposit', 'note'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'Order')
                    ->where('name', 'Pending')
                    ->value('id');
            }

            if (empty($model->order_number)) {
                $model->order_number = app(DocumentNumberService::class)
                    ->generate('order');
            }
        });
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Order'))
            ->logFillable()
            ->logOnlyDirty();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function orderItem(){
        return $this->hasOne(OrderItem::class, 'order_id');
    }
}