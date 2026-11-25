<?php

namespace App\Modules\Designation\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Designation\Repositories\Contracts\DesignationContract;
use App\Modules\Designation\Models\Designation;

class DesignationRepository extends BaseRepository implements DesignationContract
{
    public function __construct(Designation $model)
    {
        parent::__construct($model);
    }
}