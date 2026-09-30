<?php

namespace App\Models\Traits;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait ModelTrait
{
    protected $slugNum = 0;

    public function toFill(array $payload, array $exceptColumns = [])
    {
        $acceptColumns = array_diff($this->getColumns(), $exceptColumns);

        $this->fill(
            collect($payload)
                ->only($acceptColumns)
                ->all()
        );

        return $this;
    }

    public function getColumns()
    {
        if (property_exists($this, 'fillable')) {
            return $this->getFillable();
        }

        if (property_exists($this, 'guarded')) {
            $guardedColumns = $this->getGuarded();

            if (in_array('*', $guardedColumns)) {
                return Schema::getColumnListing($this->getTable());
            }

            return array_diff(
                Schema::getColumnListing($this->getTable()),
                $guardedColumns
            );
        }

        return Schema::getColumnListing($this->getTable());
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'ulid';
    }

    public function scopeSort($query)
    {
        $sortBy = request()->get('sort_by', 'id');
        $sortOrder = request()->get('sort_direction', 'desc');

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Boot ModelTrait.
     */
    protected static function bootModelTrait(): void
    {
        static::creating(function ($model) {
            if (
                $model->getConnection()
                    ->getSchemaBuilder()
                    ->hasColumn($model->getTable(), 'ulid')
                && empty($model->ulid)
            ) {
                $model->ulid = (string) Str::ulid();
            }

            if (
                $model->getConnection()
                    ->getSchemaBuilder()
                    ->hasColumn($model->getTable(), 'author_id')
                && empty($model->author_id)
            ) {
                $auth = Auth::user();

                if (!$auth) {
                    $auth = User::find(1);
                }

                if ($auth) {
                    $model->author_id = $auth->id;
                }
            }
        });
    }

    public function attachments()
    {
        return $this->morphToMany(Attachment::class, 'model');
    }

    public function author()
    {
        return $this->morphTo('author');
    }
}