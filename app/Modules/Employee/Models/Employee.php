<?php

namespace App\Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Refrence;
use App\Models\Status;
use App\Models\User;
use App\Models\Attachment;
use App\Services\DocumentNumberService;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;

class Employee extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = ['referred_by_id', 'status_id', 'avatar_id', 'cnic_front_id', 'cnic_back_id',
        'joining_date', 'name', 'father_name', 'caste', 'phone', 'email', 'address', 'note'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'Employee')
                    ->where('name', 'active')
                    ->value('id');
            }

            if (empty($model->employee_code)) {
                $model->employee_code = app(DocumentNumberService::class)
                    ->generate('employee');
            }
        });
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Employee'))
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
    public function compensations()
    {
        return $this->hasMany(EmployeeCompensation::class, 'employee_id', 'id');
    }

    public function compensation()
    {
        return $this->hasOne(EmployeeCompensation::class, 'employee_id', 'id');
    }

    public function refrence()
    {
        return $this->hasOne(Refrence::class, 'id', 'referred_by_id');
    }
    public function avatar()
    {
        return $this->belongsTo(Attachment::class, 'avatar_id');
    }
    public function cnicFront()
    {
        return $this->belongsTo(Attachment::class, 'cnic_front_id');
    }
    public function cnicBack()
    {
        return $this->belongsTo(Attachment::class, 'cnic_back_id');
    }
}
