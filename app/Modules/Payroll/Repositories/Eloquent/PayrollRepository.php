<?php

namespace App\Modules\Payroll\Repositories\Eloquent;

use App\Modules\Payroll\Models\Payroll;
use App\Modules\Payroll\Repositories\Contracts\PayrollContract;
use App\Modules\Payroll\Services\PayrollCalculationService;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class PayrollRepository extends BaseRepository implements PayrollContract
{
    public function __construct(Payroll $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        return app(PayrollCalculationService::class)->create($payload);
    }

    public function updateModel(Model $model, array $payload): Model
    {
        if (!$model instanceof Payroll) {
            throw new \InvalidArgumentException('Expected a Payroll model.');
        }

        return app(PayrollCalculationService::class)->update($model, $payload);
    }
}
