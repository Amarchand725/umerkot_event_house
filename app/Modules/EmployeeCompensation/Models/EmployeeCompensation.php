<?php

namespace App\Modules\EmployeeCompensation\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Modules\Designation\Models\Designation;
use App\Modules\Employee\Models\Employee;

class EmployeeCompensation extends Model
{
    use LogsActivity, ModelTrait, HasFactory;

    protected $fillable = [
        'employee_id', 'status_id', 'designation_id', 'pay_type',
        'effective_from', 'effective_to', 'basic_pay', 'daily_rate',
        'hourly_rate', 'commission_basis', 'commission_type', 'commission_rate', 'note',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'basic_pay' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'EmployeeCompensation')
                    ->where('name', 'active')
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
            ->useLogName(strtolower('EmployeeCompensation'))
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
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
