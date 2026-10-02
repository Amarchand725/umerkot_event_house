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
        $inventoryCategoryId = InventoryCategory::where('name', 'Bartan')->value('id');
        $pieceId = Unit::where('name', 'Piece')->value('id');

        $items = [
            [
                'name' => 'Dinner Plate',
                'price_per_unit' => 10.00,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Soup Plate',
                'price_per_unit' => 8.00,
                'total_quantity' => 50,
                'minimum_quantity' => 10,
            ],
            [
                'name' => 'Spoon',
                'price_per_unit' => 2.00,
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Fork',
                'price_per_unit' => 2.50,
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Knife',
                'price_per_unit' => 3.00,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Water Glass',
                'price_per_unit' => 5.00,
                'total_quantity' => 200,
                'minimum_quantity' => 40,
            ],
            [
                'name' => 'Tea Cup',
                'price_per_unit' => 4.00,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
        ];

        $statusId = Status::where('model', 'InventoryItem')->value('id');

        foreach ($items as $item) {
            InventoryItem::updateOrCreate([
                ...$item,
                'inventory_category_id' => $inventoryCategoryId,
                'status_id' => $statusId,
                'unit_id' => $pieceId,
            ]);
        }
    }
}
