<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Unit\Models\Unit;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $pieceId = Unit::where('name', 'Piece')->value('id');

        $inventoryCategoryId = InventoryCategory::where('name', 'Bartan')->value('id');

        $items = [
            [
                'name' => 'Dinner Plate',
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Soup Plate',
                'total_quantity' => 50,
                'minimum_quantity' => 10,
            ],
            [
                'name' => 'Spoon',
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Fork',
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Knife',
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Water Glass',
                'total_quantity' => 200,
                'minimum_quantity' => 40,
            ],
            [
                'name' => 'Tea Cup',
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
        ];

        $statusId = Status::where('model', 'InventoryItem')->value('id');

        foreach ($items as $item) {
            InventoryItem::updateOrCreate([
                ...$item,
                'price_per_unit' => fake()->numberBetween(10, 500),
                'damage_charge' => fake()->numberBetween(500, 1000),
                'replacement_charge' => fake()->numberBetween(500, 1000),
                'inventory_category_id' => $inventoryCategoryId,
                'status_id' => $statusId,
                'unit_id' => $pieceId,
            ]);
        }
    }
}
