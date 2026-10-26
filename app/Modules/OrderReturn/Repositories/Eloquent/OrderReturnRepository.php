<?php

namespace App\Modules\OrderReturn\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\OrderReturn\Repositories\Contracts\OrderReturnContract;
use App\Modules\OrderReturn\Models\OrderReturn;

class OrderReturnRepository extends BaseRepository implements OrderReturnContract
{
    public function __construct(OrderReturn $model)
    {
        parent::__construct($model);
    }
}