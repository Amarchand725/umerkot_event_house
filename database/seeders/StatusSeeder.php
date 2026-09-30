<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            //User
            ['model' => 'User', 'name' => 'active'],
            ['model' => 'User', 'name' => 'de-active'],

            //EventCategory
            ['model' => 'EventCategory', 'name' => 'active'],
            ['model' => 'EventCategory', 'name' => 'de-active'],

            //Inventory category
            ['model' => 'InventoryCategory', 'name' => 'active'],
            ['model' => 'InventoryCategory', 'name' => 'de-active'],

            //Customer
            ['model' => 'Customer', 'name' => 'active'],
            ['model' => 'Customer', 'name' => 'de-active'],

            //Unit
            ['model' => 'Unit', 'name' => 'active'],
            ['model' => 'Unit', 'name' => 'de-active'],
        ];

        foreach ($data as $item) {
            $status = Status::firstOrNew($item);
            $status->toFill($item);
            $status->save();
        }
    }
}
