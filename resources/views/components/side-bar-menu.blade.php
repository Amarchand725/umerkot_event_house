<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('back-office.auth.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <x-application-logo class="logo-full" />
                <x-favicon class="logo-mini" />
            </span>

            {{-- <span class="app-brand-text demo menu-text fw-bold ms-2">
                {{ config('app.name') }}
            </span> --}}

        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->is('back-office/auth/dashboard') ? 'active' : '' }}">
            <a href="{{ route('back-office.auth.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-home-2"></i>
                <div data-i18n="Dashboards">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">AUTH & ACCESS</span>
        </li>

        @can('notification-list')
        <li class="menu-item {{ request()->is('back-office/notifications') || request()->is('back-office/notifications/*')?'active open':'' }}">
            <a href="{{ route('back-office.notifications.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-bell"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Notifications') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['notifications'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('user-list')
        <li class="menu-item {{ request()->is('back-office/users') || request()->is('back-office/users/*')?'active open':'' }}">
            <a href="{{ route('back-office.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Users') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['users'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('customer-list')
        <li class="menu-item {{ request()->is('back-office/customers') || request()->is('back-office/customers/*')?'active open':'' }}">
            <a href="{{ route('back-office.customers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Customers') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['customers'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('role-list')
        <li class="menu-item {{ request()->is('back-office/roles') || request()->is('back-office/roles/*')?'active open':'' }}">
            <a href="{{ route('back-office.roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shield-check"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Roles') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['roles'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">CATALOG</span>
        </li>
        @can('service-list')
        <li class="menu-item {{ request()->is('back-office/services') || request()->is('back-office/services/*')?'active open':'' }}">
            <a href="{{ route('back-office.services.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Services') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['services'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('package-list')
        <li class="menu-item {{ request()->is('back-office/packages') || request()->is('back-office/packages/*')?'active open':'' }}">
            <a href="{{ route('back-office.packages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Packages') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['packages'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('unit-list')
        <li class="menu-item {{ request()->is('back-office/units') || request()->is('back-office/units/*')?'active open':'' }}">
            <a href="{{ route('back-office.units.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Units') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['units'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('inventory_category-list')
        <li class="menu-item {{ request()->is('back-office/inventory-categories') || request()->is('back-office/inventory-categories/*')?'active open':'' }}">
            <a href="{{ route('back-office.inventory-categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Inventory Categories') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['inventory_categories'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('inventory_item-list')
        <li class="menu-item {{ request()->is('back-office/inventory-items') || request()->is('back-office/inventory-items/*')?'active open':'' }}">
            <a href="{{ route('back-office.inventory-items.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Inventory items') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['inventory_items'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">EVENT</span>
        </li>
        @can('event_category-list')
        <li class="menu-item {{ request()->is('back-office/event-categories') || request()->is('back-office/event-categories/*')?'active open':'' }}">
            <a href="{{ route('back-office.event-categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Event Categories') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['event_categories'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('event-list')
        <li class="menu-item {{ request()->is('back-office/events') || request()->is('back-office/events/*')?'active open':'' }}">
            <a href="{{ route('back-office.events.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Events') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['events'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('event_service-list')
        <li class="menu-item {{ request()->is('back-office/event-services') || request()->is('back-office/event-services/*')?'active open':'' }}">
            <a href="{{ route('back-office.event-services.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Event Services') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['event-services'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('event_item-list')
        <li class="menu-item {{ request()->is('back-office/event-items') || request()->is('back-office/event-items/*')?'active open':'' }}">
            <a href="{{ route('back-office.event-items.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Event Items') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['event-items'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">BILLING</span>
        </li>
        @can('payment_method-list')
        <li class="menu-item {{ request()->is('back-office/payment-methods') || request()->is('back-office/payment-methods/*')?'active open':'' }}">
            <a href="{{ route('back-office.payment-methods.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Payment Method') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['payment_methods'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">FINANCE</span>
        </li>
        @can('expense_category-list')
        <li class="menu-item {{ request()->is('back-office/expense-categories') || request()->is('back-office/expense-categories/*')?'active open':'' }}">
            <a href="{{ route('back-office.expense-categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-activity"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Expense Categories') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['expense_categorys'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">SYSTEM</span>
        </li>
        @can('activity_log-list')
        <li class="menu-item {{ request()->is('back-office/activity-logs') || request()->is('back-office/activity-logs/*')?'active open':'' }}">
            <a href="{{ route('back-office.activity-logs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-activity"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Activity Logs') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['activity_logs'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        {{-- BILLING
        ├── Payment Methods
        ├── Payments
        └── Payment Allocations

        FINANCE
        ├── Discounts / Taxes
        └── Expenses --}}
    </ul>
</aside>
