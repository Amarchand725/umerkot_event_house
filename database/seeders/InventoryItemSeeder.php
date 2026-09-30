<?php

namespace Database\Seeders;

use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Unit\Models\Unit;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $bartan = InventoryCategory::where('name', 'Bartan')->value('id');
        $piece = Unit::where('name', 'Piece')->value('id');

        $items = [
            [
                'name' => 'Dinner Plate',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Soup Plate',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 50,
                'minimum_quantity' => 10,
            ],
            [
                'name' => 'Spoon',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Fork',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 150,
                'minimum_quantity' => 30,
            ],
            [
                'name' => 'Knife',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
            [
                'name' => 'Water Glass',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 200,
                'minimum_quantity' => 40,
            ],
            [
                'name' => 'Tea Cup',
                'inventory_category_id' => $bartan,
                'unit_id' => $piece,
                'total_quantity' => 100,
                'minimum_quantity' => 20,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate($item);
        }
    }
}
