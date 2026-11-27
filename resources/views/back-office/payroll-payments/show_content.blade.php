@php($money = fn ($amount) => 'Rs. '.number_format((float) $amount, 2))
<table class="table table-flush-spacing">
    <tr><td class="text-nowrap fw-semibold">Employee</td><td>{{ $model->payroll?->employee?->name ?? '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Payroll Period</td><td>
        {{ $model->payroll ? \Illuminate\Support\Carbon::create((int) $model->payroll->year, (int) $model->payroll->month, 1)->format('F Y') : '-' }}
    </td></tr>
    <tr><td class="text-nowrap fw-semibold">Payroll Net Payable</td><td>{{ $model->payroll ? $money($model->payroll->net_payable) : '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Payment Amount</td><td class="fw-bold">{{ $money($model->amount) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Payment Date</td><td>{{ $model->payment_date ? \Illuminate\Support\Carbon::parse($model->payment_date)->format('d M Y') : '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Payment Method</td><td>{{ $model->paymentMethod?->name ?? '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Status</td><td>{{ strtoupper($model->status?->name ?? 'UNKNOWN') }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Reference Number</td><td>{{ $model->reference_number ?: '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Notes</td><td>{{ $model->notes ?: '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Created At</td><td>{{ $model->created_at?->format('d M Y | h:i A') ?? '-' }}</td></tr>
</table>
