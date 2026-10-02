<?php

namespace App\View\Composers;

use App\Models\Role;
use App\Models\User;
use App\Models\EventService;
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

            'services'         => Service::count(),
            'packages' => Package::count(),
            'units'         => Unit::count(),
            'inventory_categories' => InventoryCategory::count(),
            'inventory_items' => InventoryItem::count(),

            'event_categories' => EventCategory::count(),
            'events' => Event::count(),
            'event_services' => EventService::count(),

            'payment_methods' => PaymentMethod::count(),

            'expense_categories' => ExpenseCategory::count(),

            'activity_logs' => ActivityLog::count(),
        ]);
    }
}
