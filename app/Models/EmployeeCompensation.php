<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;

class EmployeeCompensation extends Model
{
    use ModelTrait;

    protected $fillable = [
        'employee_id', 'status_id', 'designation_id', 'pay_type',
        'effective_from', 'effective_to', 'basic_pay', 'daily_rate',
        'hourly_rate', 'commission_basis', 'commission_type', 'commission_rate',
    ];
}
