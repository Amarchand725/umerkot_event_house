
<div class="col-12">

    {{-- Main Tabs --}}
    <div class="card border shadow-none">

        <div class="card-header border-bottom">
            <ul class="nav nav-tabs card-header-tabs"
                id="eventDetailTabs"
                role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#event-tab"
                        type="button" role="tab">
                        <i class="ti ti-calendar-event me-1"></i>
                        Event
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#customer-tab"
                        type="button" role="tab">
                        <i class="ti ti-user me-1"></i>
                        Customer
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#orders-tab"
                        type="button" role="tab">
                        <i class="ti ti-package me-1"></i>
                        Orders
                        <span class="badge bg-label-primary ms-1">
                            {{ $model->orders->count() }}
                        </span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#payments-tab"
                        type="button" role="tab">
                        <i class="ti ti-wallet me-1"></i>
                        Payments & Settlement
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#record-tab"
                        type="button" role="tab">
                        <i class="ti ti-info-circle me-1"></i>
                        Record Info
                    </button>
                </li>

            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="eventDetailTabContent">

                {{-- EVENT TAB --}}
                <div class="tab-pane fade show active"
                    id="event-tab" role="tabpanel">

                    {{-- Move your existing Event Information card here --}}
                    <div class="col-12">
                        <div class="card border shadow-none mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="ti ti-calendar-event me-1"></i>
                                    Event Information
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Event Number</small>
                                        <div class="fw-semibold">{{ $model?->event_number ?? '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Category</small>
                                        <div class="fw-semibold">{{ $model?->eventCategory?->name ?? '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Venue</small>
                                        <div class="fw-semibold">{{ $model?->venue ?? '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Start Date</small>
                                        <div class="fw-semibold">{{ $model?->start_date ? date('d M Y, h:i A', strtotime($model->start_date)) : '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">End Date</small>
                                        <div class="fw-semibold">{{ $model?->end_date ? date('d M Y, h:i A', strtotime($model->end_date)) : '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Status</small>
                                        <div>
                                            @if($model?->lastStatusLog?->status)
                                                <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($model?->lastStatusLog?->status?->name)) ?? 'bg-light text-dark' }}">
                                                    {{ strtoupper($model?->lastStatusLog?->status?->name) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    @if($model?->note)
                                        <div class="col-12">
                                            <small class="text-muted d-block">Note</small>
                                            <div>{{ $model->note }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CUSTOMER TAB --}}
                <div class="tab-pane fade"
                    id="customer-tab" role="tabpanel">

                    <div class="col-12">
                        <div class="card border shadow-none mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="ti ti-user me-1"></i>
                                    Customer Information
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Customer Name</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->name ?? $model?->customer_name ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Caste</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->caste ?? $model?->caste ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">CNIC</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->cnic_no ?? $model?->cnic_no ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Phone</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->phone ?? $model?->phone ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <small class="text-muted d-block">Alternate Phone</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->alter_phone ?? $model?->alter_phone ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <small class="text-muted d-block">Address</small>
                                        <div class="fw-semibold">
                                            {{ $model?->customer?->address ?? $model?->address ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ORDERS TAB --}}
                <div class="tab-pane fade"
                    id="orders-tab" role="tabpanel">

                    @forelse($model->orders as $order)

                        @php
                            $orderItems = $order->orderItems ?? collect();

                            $orderTabId = 'order-' . $loop->index;
                        @endphp

                        <div class="card border shadow-none mb-4">

                            <div class="card-header d-flex justify-content-between
                                        align-items-center flex-wrap gap-2">

                                <div>
                                    <h5 class="mb-1">
                                        {{ $order->order_number ?? 'Order' }}
                                    </h5>
                                    <small class="text-muted">
                                        Created:
                                        {{ $order->created_at?->format('d M Y, h:i A') ?? '-' }}
                                    </small>
                                </div>

                                <span class="badge bg-label-primary">
                                    Rs. {{ number_format((float) $order->payable_amount, 2) }}
                                </span>
                            </div>

                            <div class="card-body">

                                {{-- Order tabs --}}
                                <ul class="nav nav-pills mb-4"
                                    id="{{ $orderTabId }}-tabs"
                                    role="tablist">

                                    <li class="nav-item">
                                        <button class="nav-link active"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ $orderTabId }}-summary"
                                            type="button">
                                            Summary
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ $orderTabId }}-packages"
                                            type="button">
                                            Packages
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ $orderTabId }}-inventory"
                                            type="button">
                                            Inventory
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ $orderTabId }}-invoices"
                                            type="button">
                                            Invoices
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    {{-- SUMMARY --}}
                                    <div class="tab-pane fade show active"
                                        id="{{ $orderTabId }}-summary">

                                        <div class="row g-3">

                                            <div class="col-md-3 col-6">
                                                <small class="text-muted d-block">
                                                    Items
                                                </small>
                                                <h6>{{ $orderItems->count() }}</h6>
                                            </div>

                                            <div class="col-md-3 col-6">
                                                <small class="text-muted d-block">
                                                    Grand Total
                                                </small>
                                                <h6>
                                                    Rs. {{ number_format((float) $order->grand_total, 2) }}
                                                </h6>
                                            </div>

                                            <div class="col-md-3 col-6">
                                                <small class="text-muted d-block">
                                                    Discount
                                                </small>
                                                <h6 class="text-danger">
                                                    Rs. {{ number_format((float) $order->discount, 2) }}
                                                </h6>
                                            </div>

                                            <div class="col-md-3 col-6">
                                                <small class="text-muted d-block">
                                                    Security Deposit
                                                </small>
                                                <h6>
                                                    Rs. {{ number_format((float) $order->security_deposit, 2) }}
                                                </h6>
                                            </div>

                                            <div class="col-12">
                                                <div class="p-3 bg-light rounded">
                                                    <small class="text-muted d-block">
                                                        Payable Amount
                                                    </small>
                                                    <h4 class="mb-0 text-primary">
                                                        Rs. {{ number_format((float) $order->payable_amount, 2) }}
                                                    </h4>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- PACKAGES --}}
                                    <div class="tab-pane fade"
                                        id="{{ $orderTabId }}-packages">

                                        @php
                                            $packageItems = $orderItems->filter(
                                                fn ($item) => $item->package !== null
                                            );
                                        @endphp

                                        @forelse($packageItems as $orderItem)

                                            @php
                                                $package = $orderItem->package;
                                                $packageInventory = $package?->inventoryItems ?? collect();
                                                $packageServices = $package?->services ?? collect();
                                            @endphp

                                            <div class="card border shadow-none mb-3">

                                                <div class="card-header">
                                                    <h6 class="mb-0">
                                                        <i class="ti ti-box me-1"></i>
                                                        {{ $package?->name ?? 'Package' }}
                                                    </h6>
                                                </div>

                                                <div class="card-body">

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Package Price</small>
                                                            <strong>
                                                                Rs. {{ number_format((float) ($orderItem->price ?? $package?->price ?? 0), 2) }}
                                                            </strong>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Package Discount</small>
                                                            <strong>
                                                                Rs. {{ number_format((float) ($package?->discount ?? 0), 2) }}
                                                            </strong>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Included Inventory</small>
                                                            <strong>{{ $packageInventory->count() }} types</strong>
                                                        </div>
                                                    </div>

                                                    <p class="text-muted">
                                                        {{ $package?->description ?? 'No description available.' }}
                                                    </p>

                                                    {{-- Included inventory --}}
                                                    <h6 class="border-top pt-3">
                                                        <i class="ti ti-package me-1"></i>
                                                        Included Inventory
                                                    </h6>

                                                    <div class="table-responsive mb-4">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th class="text-end">Included Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($packageInventory as $item)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $item->inventoryItem?->name ?? $item->name ?? '-' }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ $item->pivot?->quantity ?? $item->quantity ?? 1 }}
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="2" class="text-center text-muted">
                                                                            No inventory included.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    {{-- Included services --}}
                                                    <h6>
                                                        <i class="ti ti-tools me-1"></i>
                                                        Included Services
                                                    </h6>

                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Service</th>
                                                                    <th class="text-end">Included Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($packageServices as $service)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $service->service?->name ?? $service->name ?? '-' }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ $service->pivot?->quantity ?? $service->quantity ?? 1 }}
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="2" class="text-center text-muted">
                                                                            No services included.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>

                                        @empty

                                            <p class="text-muted">No packages in this order.</p>

                                        @endforelse

                                    </div>

                                    {{-- INVENTORY --}}
                                    <div class="tab-pane fade"
                                        id="{{ $orderTabId }}-inventory">
                                        @if($orderItems->isNotEmpty())
                                            <div class="col-12">
                                                <div class="card border shadow-none mb-4">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">
                                                            <i class="ti ti-package me-1"></i>
                                                            Inventory Items
                                                            <span class="badge bg-label-secondary ms-2">
                                                                {{ $orderItems->count() }}
                                                            </span>
                                                        </h5>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Category</th>
                                                                        <th>Item</th>
                                                                        <th class="text-end">Qty</th>
                                                                        <th class="text-end">Unit Price</th>
                                                                        <th class="text-end">Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($orderItems as $orderItem)
                                                                        @php
                                                                            $quantity = ($orderItem->quantity ?? 0);
                                                                            $unitPrice = (float) ($orderItem->pivot?->unit_price ?? $orderItem->unit_price ?? 0);
                                                                            $subtotal = (float) ($orderItem->pivot?->subtotal ?? $orderItem->subtotal ?? ($quantity * $unitPrice));
                                                                            $categoryName = '-';
                                                                            $itemName = '-';
                                                                            if($orderItem->inventory_item_id != null) {
                                                                                $categoryName = 'Inventory';
                                                                                $itemName = $orderItem->item?->name ?? '-';
                                                                            } elseif ($orderItem->service_id != null) {
                                                                                $categoryName = 'Service';
                                                                                $itemName = $orderItem->service?->name ?? '-';
                                                                            }else{
                                                                                $categoryName = 'Package';
                                                                                $itemName = $orderItem->package?->name ?? '-';
                                                                            }
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $categoryName }}</td>
                                                                            <td>{{ $itemName }}</td>
                                                                            <td class="text-end">{{ $quantity }}</td>
                                                                            <td class="text-end">Rs. {{ number_format($unitPrice, 2) }}</td>
                                                                            <td class="text-end">Rs. {{ number_format($subtotal, 2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>

                                    {{-- INVOICES --}}
                                    <div class="tab-pane fade"
                                        id="{{ $orderTabId }}-invoices">
                                        <div class="card-body border-top">
                                            <h6 class="mb-3">Invoice Summary</h6>

                                            @forelse ($model->invoices ?? [] as $invoice)
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Invoice Number</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->invoice_number ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Invoice Created</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->created_at?->format('d M Y, h:i A') ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Issue Date</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->issued_at ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Due Date</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->due_at ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Discount</small>
                                                            <div class="fw-semibold text-danger">
                                                                {{ number_format((float) ($invoice->discount ?? 0), 2) }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Payable Amount</small>
                                                            <div class="fw-semibold text-primary">
                                                                {{ number_format((float) ($invoice->payable_amount ?? 0), 2) }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Payment Paid</small>
                                                            <div class="fw-semibold text-success">
                                                                {{ number_format((float) ($invoice->payment->amount ?? 0), 2) }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Outstanding Amount</small>
                                                            <div class="fw-semibold text-danger">
                                                                {{ number_format(max(0, (float) ($invoice->payable_amount ?? 0) - (float) ($invoice->payment->amount ?? 0)), 2) }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <small class="text-muted d-block">Status</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->status?->name ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <small class="text-muted d-block">Invoice Note</small>
                                                            <div class="fw-semibold">
                                                                {{ $invoice->note ?: '-' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Payment Details --}}
                                                    <div class="border-top mt-3 pt-3">
                                                        <h6 class="mb-3">Payment Details</h6>
                                                        @if($invoice->payment)
                                                            @php
                                                                $payment = $invoice->payment;
                                                            @endphp
                                                            <div class="bg-light rounded p-3 mb-2">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted d-block">Payment Number</small>
                                                                        <div class="fw-semibold">
                                                                            {{ $payment->payment_number ?? '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted d-block">Amount</small>
                                                                        <div class="fw-semibold text-success">
                                                                            {{ number_format((float) ($payment->amount ?? 0), 2) }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted d-block">Payment Method</small>
                                                                        <div class="fw-semibold">
                                                                            {{ $payment->paymentMethod?->name ?? '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted d-block">Paid At</small>
                                                                        <div class="fw-semibold">
                                                                            {{ $payment->paid_at ?? '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <small class="text-muted d-block">Payment Note</small>
                                                                        <div class="fw-semibold">
                                                                            {{ $payment->note ?: '-' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="text-muted mb-0">No payment recorded for this invoice.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0">No invoices created for this order.</p>
                                            @endforelse
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="alert alert-info">
                            No orders found for this event.
                        </div>

                    @endforelse

                </div>

                {{-- PAYMENTS TAB --}}
                <div class="tab-pane fade"
                    id="payments-tab" role="tabpanel">

                    @php
                        $orders = $model->orders;

                        $grandTotal = (float) $orders->sum('grand_total');
                        $totalDiscount = (float) $orders->sum('discount');
                        $totalPaid = (float) $orders->sum('paid_amount');

                        $totalOutstanding = (float) $orders->sum(
                            fn ($order) => max(
                                0,
                                (float) $order->payable_amount
                                - (float) $order->paid_amount
                            )
                        );

                        $totalSecurityDeposit = (float) $orders->sum('security_deposit');

                        $paymentStatus = $totalOutstanding <= 0.01
                            ? 'Paid'
                            : 'Outstanding';

                        $money = fn ($amount) => number_format((float) $amount, 2);
                    @endphp

                    <div class="card border shadow-none mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ti ti-wallet me-1"></i>
                                Event Payment Summary
                            </h5>

                            <span class="badge rounded-pill px-3 py-2
                                {{ $paymentStatus === 'Paid'
                                    ? 'bg-success'
                                    : 'bg-warning text-dark' }}">
                                {{ $paymentStatus }}
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Grand Total
                                        </small>
                                        <h5 class="mb-0">
                                            Rs. {{ $money($grandTotal) }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Total Discount
                                        </small>
                                        <h5 class="text-danger mb-0">
                                            Rs. {{ $money($totalDiscount) }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Total Paid
                                        </small>
                                        <h5 class="text-success mb-0">
                                            Rs. {{ $money($totalPaid) }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Total Outstanding
                                        </small>
                                        <h5 class="text-danger mb-0">
                                            Rs. {{ $money($totalOutstanding) }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Security Deposit
                                        </small>
                                        <h5 class="text-primary mb-0">
                                            Rs. {{ $money($totalSecurityDeposit) }}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">
                                            Payment Status
                                        </small>
                                        <h5 class="mb-0 {{ $paymentStatus === 'Paid'
                                            ? 'text-success'
                                            : 'text-warning' }}">
                                            {{ $paymentStatus }}
                                        </h5>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- RECORD TAB --}}
                <div class="tab-pane fade"
                    id="record-tab" role="tabpanel">
                    <div class="col-12">
                        <div class="card border shadow-none mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Record Information
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-6">
                                        <small class="text-muted d-block mb-2">Author</small>

                                        @if($model->author)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $model->author->name ?? '-' }}</span>
                                                    <small class="text-muted">{{ $model->author->email ?? '-' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <small class="text-muted d-block">Created At</small>
                                        <div class="fw-semibold">{{ $model?->created_at ? date('d M Y, h:i A', strtotime($model->created_at)) : '-' }}</div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <small class="text-muted d-block">Updated At</small>
                                        <div class="fw-semibold">{{ $model?->updated_at ? date('d M Y, h:i A', strtotime($model->updated_at)) : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

