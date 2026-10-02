<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'user',
                ],
                [
                    'label' => $this->makeLabel($permission),
                ]
            );
        }

        $this->command->info('Permissions created successfully.');
    }

    /**
     * Get all system permissions.
     */
    private function getPermissions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Role Management
            |--------------------------------------------------------------------------
            */

            'role-list',
            'role-create',
            'role-view',
            'role-edit',
            'role-delete',

            /*
            |--------------------------------------------------------------------------
            | User Management
            |--------------------------------------------------------------------------
            */

            'user-list',
            'user-create',
            'user-view',
            'user-edit',
            'user-delete',
            'user-status',
            'user-direct_permission',
            'user-impersonate',
            'user-change_password',

            /*
            |--------------------------------------------------------------------------
            | Permission Management
            |--------------------------------------------------------------------------
            */

            'permission-list',
            'permission-view',
            'permission-delete',

            /*
            |--------------------------------------------------------------------------
            | Status Management
            |--------------------------------------------------------------------------
            */

            'status-list',
            'status-create',
            'status-view',
            'status-edit',
            'status-delete',

            /*
            |--------------------------------------------------------------------------
            | Customer Management
            |--------------------------------------------------------------------------
            */

            'customer-list',
            'customer-create',
            'customer-view',
            'customer-edit',
            'customer-delete',
            'customer-status',

            /*
            |--------------------------------------------------------------------------
            | Notification Management
            |--------------------------------------------------------------------------
            */

            'notification-list',
            'notification-view',
            'notification-delete',

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            'activity_log-list',
            'activity_log-view',
            'activity_log-delete',

            /*
            |--------------------------------------------------------------------------
            | Event Category Management
            |--------------------------------------------------------------------------
            */

            'event_category-list',
            'event_category-create',
            'event_category-view',
            'event_category-edit',
            'event_category-delete',

            /*
            Inventory Category Management
            |--------------------------------------------------------------------------
            */
            'inventory_category-list',
            'inventory_category-create',
            'inventory_category-view',
            'inventory_category-edit',
            'inventory_category-delete',

            /*
            Customer Management
            |--------------------------------------------------------------------------
            */
            'customer-list',
            'customer-create',
            'customer-view',
            'customer-edit',
            'customer-delete',

            /*
            Unit Management
            |--------------------------------------------------------------------------
            */
            'unit-list',
            'unit-create',
            'unit-view',
            'unit-edit',
            'unit-delete',

            /*
            Inventory Item Management
            |--------------------------------------------------------------------------
            */
            'inventory_item-list',
            'inventory_item-create',
            'inventory_item-view',
            'inventory_item-edit',
            'inventory_item-delete',

            /*
            Event Management
            |--------------------------------------------------------------------------
            */
            'event-list',
            'event-create',
            'event-view',
            'event-edit',
            'event-delete',

            /*
            Payment Method Management
            |--------------------------------------------------------------------------
            */
            'payment_method-list',
            'payment_method-create',
            'payment_method-view',
            'payment_method-edit',
            'payment_method-delete',

            /*
            Payment Management
            |--------------------------------------------------------------------------
            */
            'payment-list',
            'payment-create',
            'payment-view',
            'payment-edit',
            'payment-delete',

            /*
            Package Management
            |--------------------------------------------------------------------------
            */
            'package-list',
            'package-create',
            'package-view',
            'package-edit',
            'package-delete',

            /*
            Service Management
            |--------------------------------------------------------------------------
            */
            'service-list',
            'service-create',
            'service-view',
            'service-edit',
            'service-delete',

            /*
            Expense Category Management
            |--------------------------------------------------------------------------
            */
            'expense_category-list',
            'expense_category-create',
            'expense_category-view',
            'expense_category-edit',
            'expense_category-delete',
        ];
    }

    /**
     * Generate permission label.
     */
    private function makeLabel(string $permission): string
    {
        $action = null;
        $module = $permission;

        if (str_contains($permission, '-')) {
            [$module, $action] = explode('-', $permission, 2);
        }

        $module = str_replace('_', ' ', $module);

        if ($action) {
            $action = str_replace('_', ' ', $action);

            return ucwords($module) . ' ' . ucwords($action);
        }

        return ucwords($module);
    }
}
