@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">Reports</h4>
        <p class="text-muted mb-0">
            Umerkot Event House - Business Reports
        </p>
    </div>

    <div class="row g-4 mb-4">

        @foreach([
            ['Total Events', $stats['total_events'], 'ti-calendar', 'primary'],
            ['Total Orders', $stats['total_orders'], 'ti-shopping-cart', 'success'],
            ['Total Customers', $stats['total_customers'], 'ti-users', 'info'],
            ['Payments Received', number_format($stats['total_payments'], 2), 'ti-wallet', 'warning'],
        ] as $stat)

            <div class="col-xl-3 col-md-6">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">{{ $stat[0] }}</p>
                                <h4 class="mb-0">{{ $stat[1] }}</h4>
                            </div>

                            <div class="avatar bg-label-{{ $stat[3] }}">
                                <i class="{{ $stat[2] }} fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

    </div>

    @php
        $reports = [
            ['Events', 'Event-wise records and history.', 'ti-calendar', 'reports.events'],
            ['Orders', 'Order totals, discounts and payable amounts.', 'ti-shopping-cart', 'reports.orders'],
            ['Payments', 'Received payments and outstanding balances.', 'ti-wallet', 'reports.payments'],
            ['Settlements', 'Security deposits and return deductions.', 'ti-calculator', 'reports.settlements'],
            ['Inventory', 'Issued, returned, damaged and missing items.', 'ti-package', 'reports.inventory'],
            ['Customers', 'Customer-wise business and payment history.', 'ti-users', 'reports.customers'],
        ];
    @endphp

    <div class="row g-4">
        @foreach($reports as $report)
            <div class="col-xl-4 col-md-6">
                <a href="{{ route($report[3]) }}" class="text-decoration-none">
                    <div class="card border shadow-none h-100">
                        <div class="card-body">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="avatar bg-label-primary">
                                    <i class="{{ $report[2] }} fs-3"></i>
                                </div>

                                <div>
                                    <h5 class="text-heading mb-1">
                                        {{ $report[0] }}
                                    </h5>
                                    <p class="text-muted mb-0">
                                        {{ $report[1] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

</div>
@endsection
