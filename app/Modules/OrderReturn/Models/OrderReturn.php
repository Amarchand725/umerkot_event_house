<?php

namespace App\Modules\OrderReturn\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Modules\Event\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = [
        'event_id', 'status_id', 'inspected_by', 'approved_by', 'return_number', 'returned_at', 'notes'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'OrderReturn')
                    ->where('name', 'pending')
                    ->value('id');
            }
        });
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('OrderReturn'))
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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            \App\Models\OrderReturnItem::class,
            'order_return_id'
        );
    }
}
