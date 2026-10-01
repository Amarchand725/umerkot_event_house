<?php

namespace App\Modules\Package\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Package\Repositories\Contracts\PackageContract;
use App\Modules\Package\Models\Package;

class PackageRepository extends BaseRepository implements PackageContract
{
    public function __construct(Package $model)
    {
        parent::__construct($model);
    }
}