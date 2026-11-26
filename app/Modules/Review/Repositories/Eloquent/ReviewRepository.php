<?php

namespace App\Modules\Review\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Review\Repositories\Contracts\ReviewContract;
use App\Modules\Review\Models\Review;

class ReviewRepository extends BaseRepository implements ReviewContract
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }
}