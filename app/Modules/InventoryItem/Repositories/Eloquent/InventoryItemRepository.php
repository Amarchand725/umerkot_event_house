<?php

namespace App\Modules\InventoryItem\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\InventoryItem\Repositories\Contracts\InventoryItemContract;
use App\Modules\InventoryItem\Models\InventoryItem;

class InventoryItemRepository extends BaseRepository implements InventoryItemContract
{
    public function __construct(InventoryItem $model)
    {
        parent::__construct($model);
    }
}