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
            ['model' => 'Event', 'name' => 'In Progress'],
            ['model' => 'Event', 'name' => 'Cancelled'],
            ['model' => 'Event', 'name' => 'Completed'],

            //Payment Method
            ['model' => 'PaymentMethod', 'name' => 'active'],
            ['model' => 'PaymentMethod', 'name' => 'de-active'],

            //Inventory Transaction
            ['model' => 'InventoryTransaction', 'name' => 'purchase'],
            ['model' => 'InventoryTransaction', 'name' => 'adjustment'],
            ['model' => 'InventoryTransaction', 'name' => 'reserved'],
            ['model' => 'InventoryTransaction', 'name' => 'released'],
            ['model' => 'InventoryTransaction', 'name' => 'issued'],
            ['model' => 'InventoryTransaction', 'name' => 'returned'],
            ['model' => 'InventoryTransaction', 'name' => 'damaged'],
            ['model' => 'InventoryTransaction', 'name' => 'lost'],

            //Event Return Item
            ['model' => 'EventReturnItem', 'name' => 'Pending Return'],
            ['model' => 'EventReturnItem', 'name' => 'Partially Returned'],
            ['model' => 'EventReturnItem', 'name' => 'Returned'],
            ['model' => 'EventReturnItem', 'name' => 'Damaged'],
            ['model' => 'EventReturnItem', 'name' => 'Lost'],

            //Package
            ['model' => 'Package', 'name' => 'active'],
            ['model' => 'Package', 'name' => 'de-active'],

            //Service
            ['model' => 'Service', 'name' => 'active'],
            ['model' => 'Service', 'name' => 'de-active'],

            //Expense Category
            ['model' => 'ExpenseCategory', 'name' => 'active'],
            ['model' => 'ExpenseCategory', 'name' => 'de-active'],

            //Order
            ['model' => 'Order', 'name' => 'Draft'],
            ['model' => 'Order', 'name' => 'Pending'],
            ['model' => 'Order', 'name' => 'Confirmed'],
            ['model' => 'Order', 'name' => 'In Progress'],
            ['model' => 'Order', 'name' => 'Completed'],
            ['model' => 'Order', 'name' => 'Cancelled'],

            //Payment 
            ['model' => 'Payment', 'name' => 'Pending'],
            ['model' => 'Payment', 'name' => 'Completed'],
            ['model' => 'Payment', 'name' => 'Failed'],
            ['model' => 'Payment', 'name' => 'Refunded'],

            //Invoice
            ['model' => 'Invoice', 'name' => 'Draft'],
            ['model' => 'Invoice', 'name' => 'Pending'],
            ['model' => 'Invoice', 'name' => 'Paid'],
            ['model' => 'Invoice', 'name' => 'Overdue'],
        ];

        foreach ($data as $item) {
            $status = Status::firstOrNew($item);
            $status->toFill($item);
            $status->save();
        }
    }
}
