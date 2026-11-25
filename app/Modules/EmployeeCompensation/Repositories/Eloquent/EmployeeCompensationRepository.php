<?php

namespace App\Modules\EmployeeCompensation\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\EmployeeCompensation\Repositories\Contracts\EmployeeCompensationContract;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;

class EmployeeCompensationRepository extends BaseRepository implements EmployeeCompensationContract
{
    public function __construct(EmployeeCompensation $model)
    {
        parent::__construct($model);
    }
}