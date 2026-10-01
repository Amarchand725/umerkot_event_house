<?php

namespace App\Modules\Service\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Service\Repositories\Contracts\ServiceContract;
use App\Modules\Service\Models\Service;

class ServiceRepository extends BaseRepository implements ServiceContract
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
}