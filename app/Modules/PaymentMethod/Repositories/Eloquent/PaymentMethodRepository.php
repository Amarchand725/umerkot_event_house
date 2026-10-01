<?php

namespace App\Modules\PaymentMethod\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\PaymentMethod\Repositories\Contracts\PaymentMethodContract;
use App\Modules\PaymentMethod\Models\PaymentMethod;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodContract
{
    public function __construct(PaymentMethod $model)
    {
        parent::__construct($model);
    }
}