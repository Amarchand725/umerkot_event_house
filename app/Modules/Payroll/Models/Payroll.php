<?php

namespace App\Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Models\PayrollItem;
use App\Modules\Employee\Models\Employee;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;
use App\Modules\PayrollPayment\Models\PayrollPayment;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = [
        'approved_by', 'status_id', 'employee_id', 'employee_compensation_id', 'month', 'year',
        'period_start', 'period_end', 'compensation_snapshot', 'gross_earnings', 'total_deductions',
        'net_payable', 'total_present_days', 'total_payable_days', 'total_working_hours', 'total_overtime_hours',
        'approved_at'
    ];

    protected $casts = [
        'compensation_snapshot' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'Payroll')
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
            ->useLogName(strtolower('Payroll'))
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employeeCompensation()
    {
        return $this->belongsTo(EmployeeCompensation::class, 'employee_compensation_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayrollPayment::class, 'payroll_id');
    }
}
