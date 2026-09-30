<?php

namespace App\View\Composers;

use App\Models\Role;
use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\EventCategory\Models\EventCategory;
use App\Modules\Customer\Models\Customer;
use App\Modules\Unit\Models\Unit;
use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\InventoryCategory\Models\InventoryCategory;
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
            'units'         => Unit::count(),
            'event_categories' => EventCategory::count(),
            'inventory_categories' => InventoryCategory::count(),
            'inventory_items' => InventoryItem::count(),
            'roles'         => Role::count(),
            'activity_logs' => ActivityLog::count(),
        ]);
    }
}
