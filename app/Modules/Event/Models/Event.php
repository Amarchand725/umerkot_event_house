<?php

namespace App\Modules\Event\Models;

use App\Models\Invoice;
use App\Models\LogEntityStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Modules\Customer\Models\Customer;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Services\DocumentNumberService;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Event extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = [
                'customer_id', 'event_category_id', 'event_number', 'start_date', 'end_date', 'venue','note'
            ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->event_number)) {
                $model->event_number = app(DocumentNumberService::class)
                    ->generate('event');
            }
        });
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Event'))
            ->logFillable()
            ->logOnlyDirty();
    }

    public function statusLogs()
    {
        return $this->morphMany(LogEntityStatus::class, 'model')->orderby('id', 'desc');
    }

    public function lastStatusLog()
    {
        return $this->hasOne(LogEntityStatus::class, 'model_id')
            ->where('model_type', 'Event')
            ->latestOfMany();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function eventCategory()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function orders(){
        return $this->hasMany(Order::class, 'event_id', 'id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'event_id', 'id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'event_id', 'id');
    }

    public function invoice(){
        return $this->hasOne(Invoice::class, 'event_id', 'id');
    }

    public function payment()
    {
        return $this->hasOneThrough(
            Payment::class,
            Invoice::class,
            'event_id',   // Foreign key on invoices table
            'invoice_id', // Foreign key on payments table
            'id',         // Primary key on events table
            'id'          // Primary key on invoices table
        );
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'event_id',   // Foreign key in invoices table
            'invoice_id', // Foreign key in payments table
            'id',         // Primary key in events table
            'id'          // Primary key in invoices table
        );
    }

    public function paidInvoice()
    {
        return $this->hasOne(Invoice::class, 'event_id')
            ->whereHas('status', function ($query) {
                $query->where('name', 'Paid');
            });
    }
}
