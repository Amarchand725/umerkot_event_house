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

            //Inventory Item
            ['model' => 'InventoryItem', 'name' => 'active'],
            ['model' => 'InventoryItem', 'name' => 'de-active'],

            //Event
            ['model' => 'Event', 'name' => 'pending'],
            ['model' => 'Event', 'name' => 'Getting Ready'],
            ['model' => 'Event', 'name' => 'Confirmed'],
            ['model' => 'Event', 'name' => 'Cancelled'],
            ['model' => 'Event', 'name' => 'Completed'],

            //Payment Method
            ['model' => 'PaymentMethod', 'name' => 'Unpaid'],
            ['model' => 'PaymentMethod', 'name' => 'Partially Paid'],
            ['model' => 'PaymentMethod', 'name' => 'Paid'],
            ['model' => 'PaymentMethod', 'name' => 'Overpaid'],
            ['model' => 'PaymentMethod', 'name' => 'Refund Due'],
            ['model' => 'PaymentMethod', 'name' => 'Refunded'],

            //Inventory Transaction
            ['model' => 'InventoryTransaction', 'name' => 'in'],
            ['model' => 'InventoryTransaction', 'name' => 'out'],
            ['model' => 'InventoryTransaction', 'name' => 'returned'],
            ['model' => 'InventoryTransaction', 'name' => 'damaged'],
            ['model' => 'InventoryTransaction', 'name' => 'lost'],
        ];

        foreach ($data as $item) {
            $status = Status::firstOrNew($item);
            $status->toFill($item);
            $status->save();
        }
    }
}
