{{-- Event Information --}}
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
                    <small class="text-muted d-block">Event Category</small>
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
                        @if($model->status)
                            <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($model?->status?->name)) ?? 'bg-light text-dark' }}">
                                {{ strtoupper($model?->status?->name) }}
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

{{-- Customer Information --}}
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

{{-- ============================================================
    ORDERS
============================================================ --}}
@forelse($model?->orders ?? collect() as $order)

    @php
        $orderItems = $order->orderItems ?? collect();

        $packageItems = $orderItems->filter(fn ($item) => $item->package);
        $inventoryItems = $orderItems->flatMap(
            fn ($item) => $item->inventoryItems ?? collect()
        );
        $services = $orderItems->flatMap(
            fn ($item) => $item->services ?? collect()
        );
    @endphp

    {{-- Order Summary --}}
    <div class="card-body">
        <h6 class="mb-3">Order Summary</h6>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Order Number</small>
                <div class="fw-semibold">
                    {{ $order->order_number ?? '-' }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Order Items</small>
                <div class="fw-semibold">
                    {{ $orderItems->count() }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Order Date</small>
                <div class="fw-semibold">
                    {{ $order->created_at?->format('d M Y, h:i A') ?? '-' }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Grand Total</small>
                <div class="fw-semibold">
                    {{ number_format((float) ($order->grand_total ?? 0), 2) }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Discount</small>
                <div class="fw-semibold text-danger">
                    {{ number_format((float) ($order->discount ?? 0), 2) }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Payable Amount</small>
                <div class="fw-semibold text-primary">
                    {{ number_format((float) ($order->payable_amount ?? 0), 2) }}
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted d-block">Security Deposit</small>
                <div class="fw-semibold">
                    {{ number_format((float) ($order->security_deposit ?? 0), 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Summary --}}
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

    {{-- PACKAGE --}}
    @if($packageItems->isNotEmpty())
        <div class="col-12">
            <div class="card border shadow-none mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-box me-1"></i>
                        Package Information
                    </h5>
                </div>

                <div class="card-body">
                    @foreach($packageItems as $orderItem)
                        @php
                            $package = $orderItem->package;
                        @endphp

                        <div class="{{ !$loop->last ? 'border-bottom pb-4 mb-4' : '' }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <small class="text-muted d-block">Package Name</small>
                                    <div class="fw-semibold">
                                        {{ $package->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <small class="text-muted d-block">Discount</small>
                                    <div class="fw-semibold">
                                        {{ number_format((float) ($package->discount ?? 0), 2) }}
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <small class="text-muted d-block">Package Price</small>
                                    <div class="fw-semibold">
                                        Rs. {{ number_format((float) ($orderItem->price ?? $package->price ?? 0), 2) }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <small class="text-muted d-block">Description</small>
                                    <div>{{ $package->description ?? '-' }}</div>
                                </div>
                            </div>

                            {{-- Package Includes --}}
                            @php
                                $packageInventory = $package->inventoryItems ?? collect();
                                $packageServices = $package->services ?? collect();
                            @endphp

                            @if($packageInventory->isNotEmpty() || $packageServices->isNotEmpty())
                                <div class="border-top mt-3 pt-3">
                                    <h6 class="mb-3">Package Includes</h6>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Item / Service</th>
                                                    <th class="text-end">Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($packageInventory as $packageItem)
                                                    <tr>
                                                        <td>Inventory</td>
                                                        <td>{{ $packageItem?->inventoryItem->name ?? '-' }}</td>
                                                        <td class="text-end">
                                                            {{ $packageItem->pivot?->quantity ?? $packageItem->quantity ?? 1 }}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @foreach($packageServices as $packageService)
                                                    <tr>
                                                        <td>Service</td>
                                                        <td>{{ $packageService?->service->name ?? '-' }}</td>
                                                        <td class="text-end">
                                                            {{ $packageService->pivot?->quantity ?? $packageService->quantity ?? 1 }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- INVENTORY --}}
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

@empty
    <div class="col-12">
        <div class="card border shadow-none mb-4">
            <div class="card-body text-center text-muted">
                No orders found for this event.
            </div>
        </div>
    </div>
@endforelse

{{-- Record Information --}}
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
