<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            |
            | Admin always receives ALL permissions.
            |
            */

            $admin = Role::where('name', 'Admin')
                ->where('guard_name', 'user')
                ->firstOrFail();

            $allPermissions = Permission::where('guard_name', 'user')->get();

            $admin->givePermissionTo($allPermissions);

            /*
            |--------------------------------------------------------------------------
            | Manager
            |--------------------------------------------------------------------------
            */

            $manager = Role::where('name', 'Manager')
                ->where('guard_name', 'user')
                ->firstOrFail();

            $manager->givePermissionTo([
                // Customers
                'customer-list',
                'customer-create',
                'customer-view',
                'customer-edit',
                'customer-delete',
                'customer-status',

                // Notifications
                'notification-list',
                'notification-view',

                // Activity Log
                'activity_log-list',
                'activity_log-view',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Staff
            |--------------------------------------------------------------------------
            */

            $staff = Role::where('name', 'Staff')
                ->where('guard_name', 'user')
                ->firstOrFail();

            $staff->givePermissionTo([
                // Customers
                'customer-list',
                'customer-create',
                'customer-view',
                'customer-edit',

                // Notifications
                'notification-list',
                'notification-view',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Accountant
            |--------------------------------------------------------------------------
            */

            $accountant = Role::where('name', 'Accountant')
                ->where('guard_name', 'user')
                ->firstOrFail();

            $accountant->givePermissionTo([
                // Customers
                'customer-list',
                'customer-view',

                // Notifications
                'notification-list',
                'notification-view',
            ]);

            $this->command->info(
                'Role permissions assigned successfully.'
            );
        });
    }
}