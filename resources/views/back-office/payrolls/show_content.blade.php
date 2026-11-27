@php($money = fn ($amount) => number_format((float) $amount, 2))
@php
    $compensation = $model->employeeCompensation;
    $snapshot = $model->compensation_snapshot ?? [];
    $payType = $snapshot['pay_type'] ?? $compensation?->pay_type;
    [$salaryRate, $salaryUnit] = match ($payType) {
        'monthly', 'hybrid' => [$snapshot['basic_pay'] ?? $compensation?->basic_pay, ' per month'],
        'daily' => [$snapshot['daily_rate'] ?? $compensation?->daily_rate, ' per day'],
        'hourly' => [$snapshot['hourly_rate'] ?? $compensation?->hourly_rate, ' per hour'],
        default => [$snapshot['commission_rate'] ?? $compensation?->commission_rate, ' commission'],
    };
@endphp
<table class="table table-flush-spacing">
    <tr><td class="text-nowrap fw-semibold">Employee</td><td>{{ $model->employee?->name ?? '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Joining Date</td><td>{{ $model->employee?->joining_date ? \Illuminate\Support\Carbon::parse($model->employee->joining_date)->format('d M Y') : '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Designation</td><td>{{ $compensation?->designation?->name ?? '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Salary Type</td><td>{{ \App\Enum\PayTypeEnum::tryFrom((string) $payType)?->label() ?? ucfirst((string) $payType) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Salary / Rate</td><td>{{ $salaryRate !== null ? 'Rs. '.$money($salaryRate).$salaryUnit : '-' }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Period</td><td>{{ $model->period_start?->format('d M Y') }} – {{ $model->period_end?->format('d M Y') }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Status</td><td>{{ strtoupper($model->status?->name ?? 'DRAFT') }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Present Days</td><td>{{ number_format((float) $model->total_present_days, 2) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Payable Days</td><td>{{ number_format((float) $model->total_payable_days, 2) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Working Hours</td><td>{{ number_format((float) $model->total_working_hours, 2) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Overtime Hours</td><td>{{ number_format((float) $model->total_overtime_hours, 2) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Gross Earnings</td><td>Rs. {{ $money($model->gross_earnings) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Advance Deductions</td><td>Rs. {{ $money($model->total_deductions) }}</td></tr>
    <tr><td class="text-nowrap fw-semibold">Net Payable</td><td class="fw-bold">Rs. {{ $money($model->net_payable) }}</td></tr>
</table>

<h6 class="mt-4">Payroll Items</h6>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead><tr><th>Type</th><th>Description</th><th class="text-end">Amount</th></tr></thead>
        <tbody>
            @forelse($model->items as $item)
                <tr>
                    <td>{{ ucfirst($item->item_type) }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="text-end">Rs. {{ $money($item->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No payroll items.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h6 class="mt-4">Payments</h6>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr>
        </thead>
        <tbody>
            @forelse($model->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date ? \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                    <td>{{ $payment->paymentMethod?->name ?? '-' }}</td>
                    <td>{{ $payment->reference_number ?: '-' }}</td>
                    <td class="text-end">Rs. {{ $money($payment->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No payroll payments recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
