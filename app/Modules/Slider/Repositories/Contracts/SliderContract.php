<?php

namespace App\Modules\Slider\Repositories\Contracts;

use App\Repositories\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Model;

interface SliderContract extends BaseContract
{
    public function updateStatus(Model $model, array $payload);
}
