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
        $permissions = [

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
            'event-status',
            'event-payment',
            'event-order',
            'event-return-order',
            'event-invoice',
            'event-restore',
            'event-force-delete',
            'event-bulk-delete',
            'event-bulk-restore',

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
            'payment-restore',
            'payment-force-delete',
            'payment-bulk-delete',
            'payment-bulk-restore',

            /*
            Payroll Management
            |--------------------------------------------------------------------------
            */
            'payroll-list',
            'payroll-create',
            'payroll-view',
            'payroll-edit',
            'payroll-delete',
            'payroll-restore',
            'payroll-force-delete',
            'payroll-bulk-delete',
            'payroll-bulk-restore',

            /*
            Payroll Payment Management
            |--------------------------------------------------------------------------
            */
            'payroll_payment-list',
            'payroll_payment-create',
            'payroll_payment-view',
            'payroll_payment-edit',
            'payroll_payment-delete',
            'payroll_payment-restore',
            'payroll_payment-force-delete',
            'payroll_payment-bulk-delete',
            'payroll_payment-bulk-restore',

            /*
            Package Management
            |--------------------------------------------------------------------------
            */
            'package-list',
            'package-create',
            'package-view',
            'package-edit',
            'package-delete',
            'package-restore',
            'package-force-delete',
            'package-bulk-delete',
            'package-bulk-restore',

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

            /*
            Expense Management
            |--------------------------------------------------------------------------
            */
            'expense-list',
            'expense-create',
            'expense-view',
            'expense-edit',
            'expense-delete',

            /*
            Order Management
            |--------------------------------------------------------------------------
            */
            'order-list',
            'order-create',
            'order-view',
            // 'order-edit',
            // 'order-delete',

            /*
            Reports Management
            |--------------------------------------------------------------------------
            */
            'report-list',
            'report-events',
            'report-orders',
            'report-payments',
            'report-settlements',
            'report-inventory',
            'report-customers',

            /*
            Designation Management
            |--------------------------------------------------------------------------
            */
            'designation-list',
            'designation-create',
            'designation-view',
            'designation-edit',
            'designation-delete',

            /*
            Employee Management
            |--------------------------------------------------------------------------
            */
            'employee-list',
            'employee-create',
            'employee-view',
            'employee-edit',
            'employee-delete',
            'employee-status',

            /*
            Employee Compensation Management
            |--------------------------------------------------------------------------
            */
            'employee_compensation-list',
            'employee_compensation-create',
            'employee_compensation-view',
            'employee_compensation-edit',
            'employee_compensation-delete',

            /*
            Employee Attendance Management
            |--------------------------------------------------------------------------
            */
            'employee_attendance-list',
            'employee_attendance-create',
            'employee_attendance-view',
            'employee_attendance-edit',
            'employee_attendance-delete',

            /*
            Slider Management
            |--------------------------------------------------------------------------
            */
            'slider-list',
            'slider-create',
            'slider-view',
            'slider-edit',
            'slider-delete',
            'slider-status',

            /*
            Review Management
            |--------------------------------------------------------------------------
            */
            'review-list',
            'review-create',
            'review-view',
            'review-edit',
            'review-delete',

            /*
            FrequentlyAskedQuestion Management
            |--------------------------------------------------------------------------
            */
            'frequently_asked_question-list',
            'frequently_asked_question-create',
            'frequently_asked_question-view',
            'frequently_asked_question-edit',
            'frequently_asked_question-delete',

            /*
            Blog Management
            |--------------------------------------------------------------------------
            */
            'blog-list',
            'blog-create',
            'blog-view',
            'blog-edit',
            'blog-delete',
            'blog-restore',
            'blog-force-delete',
            'blog-bulk-delete',
            'blog-bulk-restore',

            /*
            Gallery Management
            |--------------------------------------------------------------------------
            */
            'gallery-list',
            'gallery-create',
            'gallery-view',
            'gallery-edit',
            'gallery-delete',
            'gallery-restore',
            'gallery-force-delete',
            'gallery-bulk-delete',
            'gallery-bulk-restore',

            /*
            Employee Advance Management
            |--------------------------------------------------------------------------
            */
            'employee_advance-list',
            'employee_advance-create',
            'employee_advance-view',
            'employee_advance-edit',
            'employee_advance-delete',
            'employee_advance-restore',
            'employee_advance-force-delete',
            'employee_advance-bulk-delete',
            'employee_advance-bulk-restore',

            /*
            Order Return Management
            |--------------------------------------------------------------------------
            */
            'order_return-list',
            'order_return-create',
            'order_return-view',
            'order_return-edit',
            'order_return-delete',
            'order_return-restore',
            'order_return-force-delete',
            'order_return-bulk-delete',
            'order_return-bulk-restore',
        ];

        $modulesWithRecoveryActions = [
            'blog', 'customer', 'designation', 'employee_advance',
            'employee_attendance', 'employee_compensation', 'employee',
            'event_category', 'event', 'expense_category', 'expense',
            'frequently_asked_question', 'gallery', 'inventory_category',
            'inventory_item', 'order_return', 'order', 'package', 'payment_method',
            'payment', 'payroll_payment', 'payroll', 'review', 'role', 'service',
            'slider', 'unit', 'user',
        ];

        foreach ($modulesWithRecoveryActions as $module) {
            foreach (['restore', 'force-delete', 'bulk-delete', 'bulk-restore'] as $action) {
                $permissions[] = "{$module}-{$action}";
            }
        }

        return array_values(array_unique($permissions));
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
