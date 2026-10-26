<?php

namespace App\Modules\Event\Repositories\Contracts;

use App\Repositories\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Model;

interface EventContract extends BaseContract
{
    // Add Event-specific methods here if needed
    public function storePayment(Model $model, array $payload): Model;
    public function updateStatus(Model $model, array $payload);
}
