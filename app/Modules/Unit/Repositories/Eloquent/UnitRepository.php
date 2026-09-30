<?php

namespace App\Modules\Unit\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Unit\Repositories\Contracts\UnitContract;
use App\Modules\Unit\Models\Unit;

class UnitRepository extends BaseRepository implements UnitContract
{
    public function __construct(Unit $model)
    {
        parent::__construct($model);
    }
}