<?php

namespace App\Modules\Employee\Repositories\Contracts;

use App\Repositories\Contracts\BaseContract;

interface EmployeeContract extends BaseContract
{
    public function updateStatus(Model $model, array $payload);
}
