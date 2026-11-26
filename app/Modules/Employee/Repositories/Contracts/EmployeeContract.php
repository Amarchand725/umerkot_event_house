<?php

namespace App\Modules\Employee\Repositories\Contracts;

use App\Repositories\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Model;

interface EmployeeContract extends BaseContract
{
    public function updateStatus(Model $model, array $payload);
}
