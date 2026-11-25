<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refrence extends Model
{
    use SoftDeletes, ModelTrait;

    protected $fillable = ['full_name', 'phone', 'address'];
}
