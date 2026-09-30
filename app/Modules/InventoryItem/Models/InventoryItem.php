<?php

namespace App\Modules\InventoryItem\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\Unit\Models\Unit;

class InventoryItem extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = ['name', 'inventory_category_id', 'unit_id', 'sku', 'total_quantity', 'minimum_quantity', 'status_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'InventoryItem')
                    ->where('name', 'active')
                    ->value('id');
            }

            // Auto generate SKU
            if (empty($model->sku)) {
                $model->sku = static::generateSku($model->name);
            }
        });
    }

    protected static function generateSku(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));

        // First letter of every word
        $prefix = collect($words)
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        $lastSku = static::query()
            ->where('sku', 'like', "{$prefix}-%")
            ->orderByDesc('id')
            ->value('sku');

        $number = $lastSku
            ? ((int) str_replace("{$prefix}-", '', $lastSku)) + 1
            : 1;

        return sprintf('%s-%03d', $prefix, $number);
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('InventoryItem'))
            ->logFillable()
            ->logOnlyDirty();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function inventoryCategory()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
