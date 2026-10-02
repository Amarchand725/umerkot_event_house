<?php

namespace App\Modules\ExpenseCategory\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\ExpenseCategory\Repositories\Contracts\ExpenseCategoryContract;
use App\Modules\ExpenseCategory\Models\ExpenseCategory;

class ExpenseCategoryRepository extends BaseRepository implements ExpenseCategoryContract
{
    public function __construct(ExpenseCategory $model)
    {
        parent::__construct($model);
    }
}