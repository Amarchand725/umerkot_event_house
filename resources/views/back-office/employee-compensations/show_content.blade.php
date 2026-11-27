@php
    $money = fn ($amount) => $amount === null ? '-' : 'Rs. '.number_format((float) $amount, 2);
    $payType = \App\Enum\PayTypeEnum::tryFrom((string) $model->pay_type);
    [$salary, $unit] = match ($model->pay_type) {
        'monthly', 'hybrid' => [$model->basic_pay, ' per month'],
        'daily' => [$model->daily_rate, ' per day'],
        'hourly' => [$model->hourly_rate, ' per hour'],
        'commission' => [$model->commission_rate, ' commission'],
        default => [null, ''],
    };
@endphp

<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Employee</td>
        <td>{{ $model->employee?->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Joining Date</td>
        <td>{{ $model->employee?->joining_date ? \Illuminate\Support\Carbon::parse($model->employee->joining_date)->format('d M Y') : '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Designation</td>
        <td>{{ $model->designation?->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Salary Type</td>
        <td>{{ $payType?->label() ?? ucfirst((string) $model->pay_type) }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Salary / Rate</td>
        <td>{{ $money($salary) }}{{ $salary !== null ? $unit : '' }}</td>
    </tr>
    @if($model->pay_type === 'hybrid')
        <tr>
            <td class="text-nowrap fw-semibold">Commission</td>
            <td>
                {{ $model->commission_type ? ucfirst($model->commission_type) : '-' }}
                {{ $model->commission_rate !== null ? number_format((float) $model->commission_rate, 2).($model->commission_type === 'percentage' ? '%' : ' PKR') : '' }}
                {{ $model->commission_basis ? 'on '.str_replace('_', ' ', $model->commission_basis) : '' }}
            </td>
        </tr>
    @endif
    <tr>
        <td class="text-nowrap fw-semibold">Effective Period</td>
        <td>
            {{ $model->effective_from?->format('d M Y') ?? '-' }}
            – {{ $model->effective_to?->format('d M Y') ?? 'Ongoing' }}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @php($statusName = $model->status?->name ?? 'unknown')
            <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($statusName)) }}">
                {{ strtoupper($statusName) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Notes</td>
        <td>{{ $model->note ?: '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ $model->created_at?->format('d M Y | h:i A') ?? '-' }}</td>
    </tr>
</table>
