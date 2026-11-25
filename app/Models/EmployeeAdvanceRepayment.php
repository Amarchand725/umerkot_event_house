<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;

class EmployeeAdvanceRepayment extends Model
{
    use ModelTrait;

    protected $fillable = [
        'status_id', 'payment_method_id', 'employee_advance_id', 'payroll_id', 'amount', 'repayment_date',
        'reference_number', 'notes'
    ];
}
