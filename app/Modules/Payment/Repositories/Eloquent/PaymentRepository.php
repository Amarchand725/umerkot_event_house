<?php

namespace App\Modules\Payment\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Payment\Repositories\Contracts\PaymentContract;
use App\Modules\Payment\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }
}