<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Auth::loginUsingId(1);

        $this->call([
            StatusSeeder::class,
            CurrencySeeder::class,
            BusinessSettingSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            InventoryCategorySeeder::class,
            InventoryItemSeeder::class,
            EventCategorySeeder::class,
            UnitSeeder::class,
            PaymentMethodSeeder::class,
            PackageSeeder::class,
            InventoryItemSeeder::class,
        ]);
    }
}
