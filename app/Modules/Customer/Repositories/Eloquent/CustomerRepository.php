<?php

namespace App\Modules\Customer\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Customer\Repositories\Contracts\CustomerContract;
use App\Modules\Customer\Models\Customer;

class CustomerRepository extends BaseRepository implements CustomerContract
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }
}