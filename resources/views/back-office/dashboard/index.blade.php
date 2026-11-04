<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Umerkot Event House'))

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- PAGE HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Dashboard</h4>
                <p class="text-muted mb-0">
                    Welcome to {{ config('app.name', 'Umerkot Event House') }}
                </p>
            </div>

            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('back-office.orders.create') }}"
                   class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> New Order
                </a>
            </div>
        </div>

        {{-- STATISTICS --}}
        <div class="row g-4 mb-4">

            @php
                $cards = [
                    [
                        'title' => 'Total Orders',
                        'value' => number_format($totalOrders),
                        'month' => $monthlyOrders . ' this month',
                        'icon' => 'ti-shopping-cart',
                        'color' => 'primary',
                    ],
                    [
                        'title' => 'Total Payable',
                        'value' => 'Rs. ' . number_format($totalPayable, 0),
                        'month' => 'Order payable amount',
                        'icon' => 'ti-file-invoice',
                        'color' => 'info',
                    ],
                    [
                        'title' => 'Total Received',
                        'value' => 'Rs. ' . number_format($totalPaid, 0),
                        'month' => 'Rs. ' . number_format($monthlyReceived, 0) . ' this month',
                        'icon' => 'ti-wallet',
                        'color' => 'success',
                    ],
                    [
                        'title' => 'Outstanding',
                        'value' => 'Rs. ' . number_format($totalOutstanding, 0),
                        'month' => 'Remaining customer payments',
                        'icon' => 'ti-alert-circle',
                        'color' => 'danger',
                    ],
                    [
                        'title' => 'Total Invoices',
                        'value' => number_format($totalInvoices),
                        'month' => $monthlyInvoices . ' this month',
                        'icon' => 'ti-file-text',
                        'color' => 'warning',
                    ],
                    [
                        'title' => 'Upcoming Events',
                        'value' => $upcomingEvents->count(),
                        'month' => 'Next events to manage',
                        'icon' => 'ti-calendar-event',
                        'color' => 'secondary',
                    ],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted">
                                        {{ $card['title'] }}
                                    </span>
                                    <h4 class="my-2">
                                        {{ $card['value'] }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $card['month'] }}
                                    </small>
                                </div>

                                <span class="avatar avatar-md bg-label-{{ $card['color'] }} rounded">
                                    <i class="ti {{ $card['icon'] }} ti-lg"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- FINANCIAL CHART --}}
        <div class="row g-4 mb-4">

            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="mb-1">Financial Overview</h5>
                            <small class="text-muted">
                                Monthly invoice and payment performance
                            </small>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="financialChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Financial Summary</h5>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Total Payable</span>
                            <strong>
                                Rs. {{ number_format($totalPayable, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Received</span>
                            <strong class="text-success">
                                Rs. {{ number_format($totalPaid, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Outstanding</span>
                            <strong class="text-danger">
                                Rs. {{ number_format($totalOutstanding, 2) }}
                            </strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Collection Rate</span>
                            <strong>
                                {{ $totalPayable > 0
                                    ? number_format(($totalPaid / $totalPayable) * 100, 1)
                                    : 0 }}%
                            </strong>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- UPCOMING EVENTS --}}
        <div class="row g-4 mb-4">

            <div class="col-12 col-xl-6">
                <div class="card h-100">

                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Upcoming Events</h5>
                        <a href="{{ route('back-office.events.index') }}"
                           class="btn btn-sm btn-label-primary">
                            View All
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Event Date</th>
                                    <th>Venue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingEvents as $event)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">
                                                {{ $event->customer_name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $event->start_at
                                                ? \Carbon\Carbon::parse($event->start_at)->format('d M Y')
                                                : '-' }}
                                        </td>
                                        <td>{{ $event->venue ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No upcoming events found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- RECENT ORDERS --}}
            <div class="col-12 col-xl-6">
                <div class="card h-100">

                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="{{ route('back-office.orders.index') }}"
                           class="btn btn-sm btn-label-primary">
                            View All
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order No.</th>
                                    <th>Grand Total</th>
                                    <th>Payable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="fw-medium">
                                            {{ $order->order_number }}
                                        </td>
                                        <td>
                                            Rs. {{ number_format($order->grand_total, 2) }}
                                        </td>
                                        <td class="fw-semibold">
                                            Rs. {{ number_format($order->payable_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

        {{-- RECENT PAYMENTS --}}
        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Recent Payments</h5>
                <a href="{{ route('back-office.payments.index') }}"
                   class="btn btn-sm btn-label-primary">
                    View All
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Payment No.</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_number }}</td>
                                <td>
                                    {{ $payment->paid_at
                                        ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y')
                                        : '-' }}
                                </td>
                                <td>
                                    {{ $payment->paymentMethod?->name ?? '-' }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rs. {{ number_format($payment->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-label-success">
                                        {{ $payment->status?->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const element = document.querySelector('#financialChart');

                if (!element || typeof ApexCharts === 'undefined') return;

                const chart = new ApexCharts(element, {
                    series: [
                        {
                            name: 'Invoice Amount',
                            data: @json($monthlyFinancials->pluck('invoice'))
                        },
                        {
                            name: 'Received Amount',
                            data: @json($monthlyFinancials->pluck('received'))
                        }
                    ],

                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false }
                    },

                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            borderRadius: 5
                        }
                    },

                    dataLabels: {
                        enabled: false
                    },

                    xaxis: {
                        categories: @json($monthlyFinancials->pluck('month'))
                    },

                    yaxis: {
                        labels: {
                            formatter: value => 'Rs. ' + value.toLocaleString()
                        }
                    },

                    tooltip: {
                        y: {
                            formatter: value =>
                                'Rs. ' + value.toLocaleString()
                        }
                    },

                    legend: {
                        position: 'top'
                    }
                });

                chart.render();
            });
        </script>
    @endpush

</x-app-layout>