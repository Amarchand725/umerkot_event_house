<?php

namespace App\Modules\Payroll\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Payroll\Repositories\Contracts\PayrollContract;
use App\Modules\Payroll\Models\Payroll;

class PayrollRepository extends BaseRepository implements PayrollContract
{
    public function __construct(Payroll $model)
    {
        parent::__construct($model);
    }
}