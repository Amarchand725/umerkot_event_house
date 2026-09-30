<?php

namespace App\Modules\EventCategory\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\EventCategory\Repositories\Contracts\EventCategoryContract;
use App\Modules\EventCategory\Models\EventCategory;

class EventCategoryRepository extends BaseRepository implements EventCategoryContract
{
    public function __construct(EventCategory $model)
    {
        parent::__construct($model);
    }
}