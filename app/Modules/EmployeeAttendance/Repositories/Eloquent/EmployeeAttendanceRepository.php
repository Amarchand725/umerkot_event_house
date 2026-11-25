<?php

namespace App\Modules\EmployeeAttendance\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\EmployeeAttendance\Repositories\Contracts\EmployeeAttendanceContract;
use App\Modules\EmployeeAttendance\Models\EmployeeAttendance;

class EmployeeAttendanceRepository extends BaseRepository implements EmployeeAttendanceContract
{
    public function __construct(EmployeeAttendance $model)
    {
        parent::__construct($model);
    }
}