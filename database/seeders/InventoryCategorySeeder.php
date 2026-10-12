<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bartan',
            'Tent',
            'Flooring / Stage'
        ];

        $statusId = Status::class::where('model', 'InventoryCategory')
            ->where('name', 'active')
            ->value('id');

        foreach ($categories as $category) {
            InventoryCategory::updateOrCreate(
                ['name' => $category],
                [
                    'status_id' => $statusId,
                ]
            );
        }
    }
}
