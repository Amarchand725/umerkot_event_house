<?php

namespace App\Modules\Expense\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Expense\Repositories\Contracts\ExpenseContract;
use App\Modules\Expense\Models\Expense;

class ExpenseRepository extends BaseRepository implements ExpenseContract
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }
}