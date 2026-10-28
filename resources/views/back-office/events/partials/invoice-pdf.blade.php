
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .totals {
            width: 45%;
            margin-left: auto;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Event Invoice</h2>

    <p><strong>Event ID:</strong> {{ $event->id }}</p>

    <h3>Orders</h3>

    <table>
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Status</th>
                <th class="text-right">Grand Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Payable Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($event->orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->status_id }}</td>
                    <td class="text-right">
                        {{ number_format((float) $order->grand_total, 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format((float) $order->discount, 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format((float) $order->payable_amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
