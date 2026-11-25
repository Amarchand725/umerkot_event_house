<?php

namespace App\Modules\EmployeeAdvance\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\EmployeeAdvance\Repositories\Contracts\EmployeeAdvanceContract;
use App\Modules\EmployeeAdvance\Models\EmployeeAdvance;

class EmployeeAdvanceRepository extends BaseRepository implements EmployeeAdvanceContract
{
    public function __construct(EmployeeAdvance $model)
    {
        parent::__construct($model);
    }
}