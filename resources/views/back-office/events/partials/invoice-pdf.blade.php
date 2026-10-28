<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Invoice</title>

    <style>
        @page {
            margin: 28px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #263238;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            margin-bottom: 22px;
            border-bottom: 3px solid #243b53;
            padding-bottom: 15px;
        }

        .header td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #243b53;
        }

        .invoice-title {
            font-size: 25px;
            font-weight: bold;
            color: #243b53;
            text-align: right;
        }

        .muted {
            color: #718096;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #243b53;
            margin: 18px 0 7px;
            padding-bottom: 5px;
            border-bottom: 1px solid #d9e2ec;
        }

        .info-table,
        .items-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
        }

        .value {
            font-weight: bold;
            font-size: 10px;
            color: #263238;
        }

        .items-table {
            margin-bottom: 12px;
        }

        .items-table th {
            background: #243b53;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #243b53;
        }

        .items-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .empty {
            text-align: center;
            color: #718096;
            padding: 12px !important;
        }

        .summary-wrapper {
            width: 100%;
            margin-top: 18px;
        }

        .summary-table {
            width: 55%;
            margin-left: auto;
        }

        .summary-table td {
            padding: 7px 9px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-table .total td {
            background: #243b53;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 10px 9px;
        }

        .summary-table .balance td {
            background: #eaf2f8;
            color: #243b53;
            font-size: 11px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #d9e2ec;
            text-align: center;
            color: #718096;
            font-size: 9px;
        }

        .order-heading {
            background: #eaf0f6;
            padding: 8px;
            margin-top: 14px;
            font-size: 11px;
            font-weight: bold;
            color: #243b53;
        }

        .page-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $money = fn ($amount) => number_format((float) $amount, 2);

        $orders = $event->orders;

        $totalOrderAmount = 0;
        $totalDiscount = 0;
        $totalPayable = 0;
        $totalPaid = 0;
        $totalOutstanding = 0;

        foreach ($orders as $order) {
            $orderGrandTotal = (float) ($order->grand_total ?? 0);

            $orderDiscount = (float) ($order->discount ?? 0);
            $invoiceDiscount = (float) ($order->invoice?->discount ?? 0);

            $combinedDiscount = $orderDiscount + $invoiceDiscount;

            $payableAmount = max(
                0,
                $orderGrandTotal - $combinedDiscount
            );

            $orderPaid = (float) (
                $order->invoice?->payments
                    ?->filter(
                        fn ($payment) =>
                            $payment->status?->name === 'Completed'
                    )
                    ->sum('amount') ?? 0
            );

            $orderOutstanding = max(
                0,
                $payableAmount - $orderPaid
            );

            $totalOrderAmount += $orderGrandTotal;
            $totalDiscount += $combinedDiscount;
            $totalPayable += $payableAmount;
            $totalPaid += $orderPaid;
            $totalOutstanding += $orderOutstanding;
        }
    @endphp

    {{-- Invoice Header --}}
    <table class="header">
        <tr>
            <td>
                <div class="brand">EVENT MANAGEMENT</div>
                <div class="muted">Event Invoice &amp; Order Summary</div>
            </td>
            <td>
                <div class="invoice-title">INVOICE</div>
                <div class="text-right">
                    <strong>Invoice Date:</strong>
                    {{ $event?->invoice?->issued_at }}
                </div>
                <div class="text-right">
                    <strong>Event ID:</strong> {{ $event->event_number }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Customer and Event Details --}}
    <div class="section-title">Customer &amp; Event Information</div>

    <table class="info-table">
        <tr>
            <td>
                <div class="label">Customer</div>
                <div class="value">
                    {{ data_get($event, 'customer.name', 'N/A') }}
                </div>

                <div class="label" style="margin-top: 8px;">Email</div>
                <div>
                    {{ data_get($event, 'customer.email', 'N/A') }}
                </div>

                <div class="label" style="margin-top: 8px;">Phone</div>
                <div>
                    {{ data_get($event, 'customer.phone', 'N/A') }}
                </div>
            </td>

            <td>
                <div class="label">Event Name</div>
                <div class="value">
                    {{ data_get($event?->eventCategory, 'name', data_get($event, 'title', 'Event')) }}
                </div>

                <div class="label" style="margin-top: 8px;">Event Date</div>
                <div>
                    {{ data_get($event, 'start_date')
                        ? \Carbon\Carbon::parse($event->start_date)->format('d M Y')
                        : 'N/A' }}
                        -
                    {{ data_get($event, 'end_date')
                        ? \Carbon\Carbon::parse($event->end_date)->format('d M Y')
                        : 'N/A' }}
                </div>

                <div class="label" style="margin-top: 8px;">Venue</div>
                <div>
                    {{ data_get($event, 'venue', data_get($event, 'location', 'N/A')) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Orders Overview --}}
    <div class="section-title">Orders Overview</div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Status</th>
                <th class="text-right">Grand Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Outstanding</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($orders as $order)
                @php
                    $orderDiscount = (float) ($order->discount ?? 0);
                    $invoiceDiscount = (float) ($order->invoice?->discount ?? 0);
                    $combinedDiscount = $orderDiscount + $invoiceDiscount;

                    // Assumes grand_total is before both order and invoice discounts.
                    $payableAmount = max(
                        0,
                        (float) $order->grand_total - $combinedDiscount
                    );

                    $orderPaid = (float) (
                        $order->invoice?->payments
                            ?->filter(fn ($payment) => $payment->status?->name === 'Completed')
                            ->sum('amount') ?? 0
                    );

                    $orderOutstanding = max(0, $payableAmount - $orderPaid);
                @endphp
                <tr>
                    <td>{{ $order->order_number ?? 'N/A' }}</td>
                    <td>{{ data_get($order, 'status.name', 'N/A') }}</td>
                    <td class="text-right">{{ $money($order->grand_total) }}</td>
                    <td class="text-right">{{ $money($order->discount) }}</td>
                    <td class="text-right">{{ $money($orderPaid) }}</td>
                    <td class="text-right">{{ $money($orderOutstanding) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">No orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Order Items --}}
    @foreach ($orders as $order)
        <div class="order-heading">
            Order: {{ $order->order_number ?? 'N/A' }}
        </div>

        {{-- Inventory --}}
        <div class="section-title">Inventory</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Item</th>
                    <th class="text-center" style="width: 15%;">Quantity</th>
                    <th style="width: 20%;">Unit Price</th>
                    <th class="text-right" style="width: 23%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                {{-- Replace inventoryItems and fields with actual relationships --}}
                @forelse ($order->orderItems ?? [] as $orderItem)
                    @php
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
                        <td>{{ $categoryName ?? 'N/A' }}</td>
                        <td>{{ $itemName ?? 'N/A' }}</td>
                        <td class="text-center">{{ data_get($orderItem, 'pivot.quantity', data_get($orderItem, 'quantity', 0)) }}</td>
                        <td>{{ $money(data_get($orderItem, 'pivot.unit_price', data_get($orderItem, 'unit_price', data_get($orderItem, 'price', 0)))) }}</td>
                        <td class="text-right">
                            {{ $money(
                                data_get($orderItem, 'pivot.subtotal',
                                    data_get($orderItem, 'subtotal',
                                        (float) data_get($orderItem, 'pivot.quantity', data_get($orderItem, 'quantity', 0))
                                        * (float) data_get($orderItem, 'pivot.unit_price', data_get($orderItem, 'unit_price', data_get($orderItem, 'price', 0)))
                                    )
                                )
                            ) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">No inventory items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    {{-- Financial Summary --}}
    <div class="section-title">Payment Summary</div>

    <table class="summary-table">
        <tr>
            <td>Grand Total</td>
            <td class="text-right">
                {{ $money($totalOrderAmount) }}
            </td>
        </tr>

        <tr>
            <td>Total Discount</td>
            <td class="text-right">
                - {{ $money($totalDiscount) }}
            </td>
        </tr>

        <tr class="total">
            <td>Total Payable</td>
            <td class="text-right">
                {{ $money($totalPayable) }}
            </td>
        </tr>

        <tr>
            <td>Paid Amount</td>
            <td class="text-right">
                {{ $money($totalPaid) }}
            </td>
        </tr>

        <tr class="balance">
            <td>Outstanding Balance</td>
            <td class="text-right">
                {{ $money($totalOutstanding) }}
            </td>
        </tr>

        <tr>
            <td>Payment Status</td>
            <td class="text-right">
                <strong>
                    {{ $totalOutstanding <= 0 ? 'PAID' : ($totalPaid > 0 ? 'PARTIALLY PAID' : 'UNPAID') }}
                </strong>
            </td>
        </tr>
    </table>

    <div class="footer">
        Thank you for your business.
        <br>
        This invoice was generated electronically.
    </div>
</body>
</html>
