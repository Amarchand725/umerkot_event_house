<?php

return [
    \App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract::class => \App\Modules\BusinessSetting\Repositories\Eloquent\BusinessSettingRepository::class,
    \App\Modules\Role\Repositories\Contracts\RoleContract::class => \App\Modules\Role\Repositories\Eloquent\RoleRepository::class,
    \App\Modules\User\Repositories\Contracts\UserContract::class => \App\Modules\User\Repositories\Eloquent\UserRepository::class,
    \App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract::class => \App\Modules\ActivityLog\Repositories\Eloquent\ActivityLogRepository::class,
    \App\Modules\EventCategory\Repositories\Contracts\EventCategoryContract::class => \App\Modules\EventCategory\Repositories\Eloquent\EventCategoryRepository::class,
];
