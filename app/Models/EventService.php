<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventService extends Model
{
    use ModelTrait, SoftDeletes;

    protected $fillable = ['event_id', 'service_id', 'quantity', 'price', 'subtotal', 'notes'];
}
