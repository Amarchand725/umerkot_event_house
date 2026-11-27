<?php

namespace App\View\Composers;

use App\Models\Role;
use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\Customer\Models\Customer;
use App\Modules\Unit\Models\Unit;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Event\Models\Event;
use App\Modules\InventoryCategory\Models\InventoryCategory;
use App\Modules\PaymentMethod\Models\PaymentMethod;
use App\Modules\Package\Models\Package;
use App\Modules\Service\Models\Service;
use App\Modules\ExpenseCategory\Models\ExpenseCategory;
use App\Modules\Expense\Models\Expense;
use App\Modules\Payment\Models\Payment;
use App\Modules\Employee\Models\Employee;
use App\Modules\Designation\Models\Designation;
use App\Modules\EmployeeAdvance\Models\EmployeeAdvance;
use App\Modules\EmployeeCompensation\Models\EmployeeCompensation;
use App\Modules\Order\Models\Order;
use App\Modules\OrderReturn\Models\OrderReturn;
use App\Modules\Payroll\Models\Payroll;
use App\Modules\PayrollPayment\Models\PayrollPayment;
use App\Modules\Slider\Models\Slider;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $notificationCount = 0;

        $view->with('sidebarCounts', [
            'notifications' => $notificationCount,
            'users'         => User::count(),
            'customers'         => Customer::count(),
            'roles'         => Role::count(),

            'events' => Event::count(),
            'orders' => Order::count(),
            'order_returns' => OrderReturn::count(),
            'packages' => Package::count(),
            'expenses' => Expense::count(),

            'payments' => Payment::count(),

            'employees' => Employee::count(),
            'designations' => Designation::count(),
            'employee_compensations' => EmployeeCompensation::count(),
            'employee_advances' => EmployeeAdvance::count(),
            'payrolls' => Payroll::count(),
            'payroll_payments' => PayrollPayment::count(),

            'services'         => Service::count(),
            'units'         => Unit::count(),
            'inventory_categories' => InventoryCategory::count(),
            'sliders' => Slider::count(),
            'inventory_items' => InventoryItem::count(),
            'event_categories' => EventCategory::count(),
            'payment_methods' => PaymentMethod::count(),
            'expense_categories' => ExpenseCategory::count(),

            'activity_logs' => ActivityLog::count(),
        ]);
    }
}
