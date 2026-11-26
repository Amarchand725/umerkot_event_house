<?php

namespace App\Modules\FrequentlyAskedQuestion\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\FrequentlyAskedQuestion\Repositories\Contracts\FrequentlyAskedQuestionContract;
use App\Modules\FrequentlyAskedQuestion\Models\FrequentlyAskedQuestion;

class FrequentlyAskedQuestionRepository extends BaseRepository implements FrequentlyAskedQuestionContract
{
    public function __construct(FrequentlyAskedQuestion $model)
    {
        parent::__construct($model);
    }
}