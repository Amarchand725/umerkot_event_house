<?php

namespace App\Modules\PayrollPayment\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\PayrollPayment\Repositories\Contracts\PayrollPaymentContract;
use App\Modules\PayrollPayment\Models\PayrollPayment;

class PayrollPaymentRepository extends BaseRepository implements PayrollPaymentContract
{
    public function __construct(PayrollPayment $model)
    {
        parent::__construct($model);
    }
}