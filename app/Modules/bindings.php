<?php

return [
    \App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract::class => \App\Modules\BusinessSetting\Repositories\Eloquent\BusinessSettingRepository::class,
    \App\Modules\Role\Repositories\Contracts\RoleContract::class => \App\Modules\Role\Repositories\Eloquent\RoleRepository::class,
    \App\Modules\User\Repositories\Contracts\UserContract::class => \App\Modules\User\Repositories\Eloquent\UserRepository::class,
    \App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract::class => \App\Modules\ActivityLog\Repositories\Eloquent\ActivityLogRepository::class,
    \App\Modules\EventCategory\Repositories\Contracts\EventCategoryContract::class => \App\Modules\EventCategory\Repositories\Eloquent\EventCategoryRepository::class,
    \App\Modules\InventoryCategory\Repositories\Contracts\InventoryCategoryContract::class => \App\Modules\InventoryCategory\Repositories\Eloquent\InventoryCategoryRepository::class,
    \App\Modules\Customer\Repositories\Contracts\CustomerContract::class => \App\Modules\Customer\Repositories\Eloquent\CustomerRepository::class,
    \App\Modules\Unit\Repositories\Contracts\UnitContract::class => \App\Modules\Unit\Repositories\Eloquent\UnitRepository::class,
    \App\Modules\InventoryItem\Repositories\Contracts\InventoryItemContract::class => \App\Modules\InventoryItem\Repositories\Eloquent\InventoryItemRepository::class,
    \App\Modules\Event\Repositories\Contracts\EventContract::class => \App\Modules\Event\Repositories\Eloquent\EventRepository::class,
    \App\Modules\Payment\Repositories\Contracts\PaymentContract::class => \App\Modules\Payment\Repositories\Eloquent\PaymentRepository::class,
    \App\Modules\PaymentMethod\Repositories\Contracts\PaymentMethodContract::class => \App\Modules\PaymentMethod\Repositories\Eloquent\PaymentMethodRepository::class,
    \App\Modules\Service\Repositories\Contracts\ServiceContract::class => \App\Modules\Service\Repositories\Eloquent\ServiceRepository::class,
    \App\Modules\EventAddition\Repositories\Contracts\EventAdditionContract::class => \App\Modules\EventAddition\Repositories\Eloquent\EventAdditionRepository::class,
    \App\Modules\Package\Repositories\Contracts\PackageContract::class => \App\Modules\Package\Repositories\Eloquent\PackageRepository::class,
    \App\Modules\ExpenseCategory\Repositories\Contracts\ExpenseCategoryContract::class => \App\Modules\ExpenseCategory\Repositories\Eloquent\ExpenseCategoryRepository::class,
];
