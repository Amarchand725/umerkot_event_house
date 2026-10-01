<?php

namespace App\Modules\Event\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Models\Event;

class EventRepository extends BaseRepository implements EventContract
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }
}