<?php

namespace App\Modules\InventoryCategory\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\InventoryCategory\Repositories\Contracts\InventoryCategoryContract;
use App\Modules\InventoryCategory\Models\InventoryCategory;

class InventoryCategoryRepository extends BaseRepository implements InventoryCategoryContract
{
    public function __construct(InventoryCategory $model)
    {
        parent::__construct($model);
    }
}