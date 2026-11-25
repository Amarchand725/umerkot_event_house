<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;

class PayrollItem extends Model
{
    use ModelTrait;

    protected $fillable = ['payroll_id', 'item_type', 'description', 'amount', 'source'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
