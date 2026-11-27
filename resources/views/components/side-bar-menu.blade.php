
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- BRAND --}}
    <div class="app-brand demo">
        <a href="{{ route('back-office.auth.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <x-application-logo class="logo-full" />
                <x-favicon class="logo-mini" />
            </span>
        </a>

        <a href="javascript:void(0);"
           class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-2">

        {{-- ===================================================== --}}
        {{-- OVERVIEW --}}
        {{-- ===================================================== --}}

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Overview</span>
        </li>

        <li class="menu-item {{ request()->is('back-office/auth/dashboard') ? 'active' : '' }}">
            <a href="{{ route('back-office.auth.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>


        {{-- ===================================================== --}}
        {{-- ADMINISTRATION --}}
        {{-- ===================================================== --}}

        @canany(['notification-list', 'user-list', 'customer-list', 'role-list'])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Administration</span>
            </li>

            {{-- AUTH & ACCESS --}}
            <li class="menu-item {{ request()->is('back-office/notifications*', 'back-office/users*', 'back-office/customers*', 'back-office/roles*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>Auth & Access</span>
                    </div>
                </a>

                <ul class="menu-sub">

                    @can('notification-list')
                        <li class="menu-item {{ request()->is('back-office/notifications*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.notifications.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-bell"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Notifications') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['notifications'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('user-list')
                        <li class="menu-item {{ request()->is('back-office/users*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.users.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-users"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Users') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['users'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('customer-list')
                        <li class="menu-item {{ request()->is('back-office/customers*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.customers.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-user-square"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Customers') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['customers'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('role-list')
                        <li class="menu-item {{ request()->is('back-office/roles*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.roles.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-shield-check"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Roles') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['roles'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

        @endcanany

        {{-- ===================================================== --}}
        {{-- BOOKINGS & OPERATIONS --}}
        {{-- ===================================================== --}}

@canany(['event-list', 'package-list', 'expense-list', 'order-list', 'order_return-list'])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Operations</span>
            </li>

            {{-- EVENTS --}}
            <li class="menu-item {{ request()->is('back-office/events*', 'back-office/event-categories*', 'back-office/orders*', 'back-office/order-returns*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>Bookings</span>
                    </div>
                </a>

                <ul class="menu-sub">

                    @can('event-list')
                        <li class="menu-item {{ request()->is('back-office/events*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.events.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-calendar"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Events') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['events'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('order-list')
                        <li class="menu-item {{ request()->is('back-office/orders*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.orders.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Orders') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['orders'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('order_return-list')
                        <li class="menu-item {{ request()->is('back-office/order-returns*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.order-returns.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-arrow-back-up"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Order Returns') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['order_returns'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('package-list')
                        <li class="menu-item {{ request()->is('back-office/packages*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.packages.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-box-seam"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Packages') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['packages'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('expense-list')
                        <li class="menu-item {{ request()->is('back-office/expenses*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.expenses.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-receipt"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Expenses') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['expenses'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

        @endcanany

        {{-- ===================================================== --}}
        {{-- FINANCE --}}
        {{-- ===================================================== --}}

@canany(['payment-list', 'payroll_payment-list'])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Payments</span>
            </li>

            {{-- PAYMENTS --}}
            <li class="menu-item {{ request()->is('back-office/payments*', 'back-office/payroll-payments*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>Finance</span>
                    </div>
                </a>

                <ul class="menu-sub">

                    @can('payment-list')
                        <li class="menu-item {{ request()->is('back-office/payments*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.payments.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-calendar"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Payments') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['payments'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('payroll_payment-list')
                        <li class="menu-item {{ request()->is('back-office/payroll-payments*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.payroll-payments.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-cash-banknote"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Payroll Payments') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['payroll_payments'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

        @endcanany

        {{-- ===================================================== --}}
        {{-- ANALYTICS --}}
        {{-- ===================================================== --}}

        @canany([
            'report-list',
            'report-events',
            'report-orders',
            'report-payments',
            'report-settlements',
            'report-inventory',
            'report-customers'
        ])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Analytics</span>
            </li>

            <li class="menu-item {{ request()->is('back-office/reports', 'back-office/reports/*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>Reports</span>
                        <span class="badge bg-primary">{{ $sidebarCounts['reports'] ?? 0 }}</span>
                    </div>
                </a>

                <ul class="menu-sub">

                    @can('report-list')
                        <li class="menu-item {{ request()->is('back-office/reports') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                                <div>All Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-events')
                        <li class="menu-item {{ request()->is('back-office/reports/events') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.events') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-calendar-stats"></i>
                                <div>Event Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-orders')
                        <li class="menu-item {{ request()->is('back-office/reports/orders') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.orders') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                                <div>Order Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-payments')
                        <li class="menu-item {{ request()->is('back-office/reports/payments') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.payments') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-cash"></i>
                                <div>Payment Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-settlements')
                        <li class="menu-item {{ request()->is('back-office/reports/settlements') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.settlements') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-scale"></i>
                                <div>Settlement Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-inventory')
                        <li class="menu-item {{ request()->is('back-office/reports/inventory') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.inventory') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-package"></i>
                                <div>Inventory Reports</div>
                            </a>
                        </li>
                    @endcan

                    @can('report-customers')
                        <li class="menu-item {{ request()->is('back-office/reports/customers') ? 'active' : '' }}">
                            <a href="{{ route('back-office.reports.customers') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-user-search"></i>
                                <div>Customer Reports</div>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

        @endcanany

        {{-- ===================================================== --}}
        {{-- EMPLOYEE --}}
        {{-- ===================================================== --}}

        @canany([
            'employee-list',
            'employee_attendance-list',
            'employee_compensation-list',
            'employee_advance-list',
            'payroll-list',
            'designation-list',
        ])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Employee</span>
            </li>

            <li class="menu-item {{ request()->is('back-office/employees*', 'back-office/employee-attendances*', 'back-office/employee-compensations*', 'back-office/employee-advances*', 'back-office/payrolls*', 'back-office/designations*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>Employees</span>
                        <span class="badge bg-primary">{{ $sidebarCounts['employees'] ?? 0 }}</span>
                    </div>
                </a>

                <ul class="menu-sub">

                    @can('employee-list')
                        <li class="menu-item {{ request()->is('back-office/employees') ? 'active' : '' }}">
                            <a href="{{ route('back-office.employees.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                                <div>All Employees</div>
                            </a>
                        </li>
                    @endcan

                    @can('designation-list')
                        <li class="menu-item {{ request()->is('back-office/designations*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.designations.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Designations') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['designations'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('employee_attendance-list')
                        <li class="menu-item {{ request()->is('back-office/employee-attendances*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.employee-attendances.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                                <div>All Attendance</div>
                            </a>
                        </li>
                    @endcan

                    @can('employee_compensation-list')
                        <li class="menu-item {{ request()->is('back-office/employee-compensations*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.employee-compensations.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-cash"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Employee Compensation') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['employee_compensations'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('employee_advance-list')
                        <li class="menu-item {{ request()->is('back-office/employee-advances*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.employee-advances.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-cash-plus"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Employee Advances') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['employee_advances'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('payroll-list')
                        <li class="menu-item {{ request()->is('back-office/payrolls*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.payrolls.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-report-money"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Payroll') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['payrolls'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

        @endcanany


        {{-- ===================================================== --}}
        {{-- PRODUCT MANAGEMENT --}}
        {{-- ===================================================== --}}

        @canany([
            'slider-list',
            'inventory_item-list',
            'service-list',
            'package-list',
            'inventory_category-list',
            'unit-list',
            'event_category-list',
            'expense_category-list',
            'payment_method-list',
        ])

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Product Management</span>
            </li>

            <li class="menu-item {{ request()->is('back-office/inventory-items*', 'back-office/services*', 'back-office/packages*', 'back-office/inventory-categories*', 'back-office/units*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-box"></i>
                    <div>Catalog</div>
                </a>

                <ul class="menu-sub">

                    @can('slider-list')
                        <li class="menu-item {{ request()->is('back-office/sliders*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.sliders.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-package"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Inventory Items') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['sliders'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('inventory_item-list')
                        <li class="menu-item {{ request()->is('back-office/inventory-items*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.inventory-items.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-package"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Inventory Items') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['inventory_items'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('service-list')
                        <li class="menu-item {{ request()->is('back-office/services*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.services.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-tool"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Services') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['services'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('inventory_category-list')
                        <li class="menu-item {{ request()->is('back-office/inventory-categories*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.inventory-categories.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-category"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Inventory Categories') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['inventory_categories'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('unit-list')
                        <li class="menu-item {{ request()->is('back-office/units*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.units.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-ruler-measure"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Units') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['units'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('event_category-list')
                        <li class="menu-item {{ request()->is('back-office/event-categories*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.event-categories.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-category"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Event Categories') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['event_categories'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('expense_category-list')
                        <li class="menu-item {{ request()->is('back-office/expense-categories*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.expense-categories.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-category"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Expense Categories') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['expense_categories'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('payment_method-list')
                        <li class="menu-item {{ request()->is('back-office/payment-methods*') ? 'active' : '' }}">
                            <a href="{{ route('back-office.payment-methods.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-credit-card"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <span>{{ module_label('list', 'Payment Methods') }}</span>
                                    <span class="badge bg-primary">{{ $sidebarCounts['payment_methods'] ?? 0 }}</span>
                                </div>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

        @endcanany


        {{-- ===================================================== --}}
        {{-- SYSTEM --}}
        {{-- ===================================================== --}}

        @can('activity_log-list')

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">System</span>
            </li>

            <li class="menu-item {{ request()->is('back-office/activity-logs*') ? 'active' : '' }}">
                <a href="{{ route('back-office.activity-logs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-history"></i>
                    <div class="d-flex justify-content-between w-100">
                        <span>{{ module_label('list', 'Activity Logs') }}</span>
                        <span class="badge bg-primary">{{ $sidebarCounts['activity_logs'] ?? 0 }}</span>
                    </div>
                </a>
            </li>

        @endcan

    </ul>
</aside>
