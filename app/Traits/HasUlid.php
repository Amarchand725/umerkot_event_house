<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    protected static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (! $model->ulid) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }
}