<?php

namespace App\Models;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use ModelTrait;

    protected $fillable = [
        'payroll_id', 'item_type', 'description', 'amount', 'source_id', 'source_type',
    ];

    public function source()
    {
        return $this->morphTo();
    }
}
