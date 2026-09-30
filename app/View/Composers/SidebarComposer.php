<?php

namespace App\View\Composers;

use App\Models\Role;
use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\EventCategory\Models\EventCategory;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $notificationCount = 0;

        $view->with('sidebarCounts', [
            'notifications' => $notificationCount,
            'users'         => User::count(),
            'roles'         => Role::count(),
            'activity_logs' => ActivityLog::count(),
            'event_categories' => EventCategory::count(),
        ]);
    }
}