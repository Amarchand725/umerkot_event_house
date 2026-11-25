<?php

namespace App\Modules\Employee\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Employee\Repositories\Contracts\EmployeeContract;
use App\Modules\Employee\Models\Employee;

class EmployeeRepository extends BaseRepository implements EmployeeContract
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }
}