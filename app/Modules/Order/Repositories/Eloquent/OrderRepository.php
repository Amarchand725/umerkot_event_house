<?php

namespace App\Modules\Order\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Order\Repositories\Contracts\OrderContract;
use App\Modules\Order\Models\Order;

class OrderRepository extends BaseRepository implements OrderContract
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}