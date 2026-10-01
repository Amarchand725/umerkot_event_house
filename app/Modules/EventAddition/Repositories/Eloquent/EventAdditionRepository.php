<?php

namespace App\Modules\EventAddition\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\EventAddition\Repositories\Contracts\EventAdditionContract;
use App\Modules\EventAddition\Models\EventAddition;

class EventAdditionRepository extends BaseRepository implements EventAdditionContract
{
    public function __construct(EventAddition $model)
    {
        parent::__construct($model);
    }
}