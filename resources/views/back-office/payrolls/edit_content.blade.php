@method('PUT')
<div class="row g-3 mb-4">
    <input type="hidden" name="employee_id" id="payroll-employee-id"
           value="{{ old('employee_id', $model->employee?->ulid) }}">

    <div class="col-12">
        <label for="employee_compensation_id" class="form-label fw-semibold">
            Employee Compensation <span class="text-danger">*</span>
        </label>
        <select id="employee_compensation_id" name="employee_compensation_id" class="form-select">
            <option value="">Select employee compensation</option>
            @foreach($compensations as $compensation)
                @php
                    [$listedRate, $listedUnit] = match ($compensation->pay_type) {
                        'monthly', 'hybrid' => [$compensation->basic_pay, '/ month'],
                        'daily' => [$compensation->daily_rate, '/ day'],
                        'hourly' => [$compensation->hourly_rate, '/ hour'],
                        default => [$compensation->commission_rate, 'commission'],
                    };
                @endphp
                <option value="{{ $compensation->ulid }}"
                        data-employee="{{ $compensation->employee?->ulid }}"
                        @selected(old('employee_compensation_id', $model->employeeCompensation?->ulid) === $compensation->ulid)>
                    {{ $compensation->employee?->name ?? 'Unknown employee' }}
                    @if($compensation->employee?->joining_date)
                        (Joined {{ \Illuminate\Support\Carbon::parse($compensation->employee->joining_date)->format('d M Y') }})
                    @endif
                    — {{ \App\Enum\PayTypeEnum::tryFrom((string) $compensation->pay_type)?->label() ?? ucfirst($compensation->pay_type ?? 'unspecified') }}
                    — Rs. {{ number_format((float) $listedRate, 2) }} {{ $listedUnit }}
                    @if($compensation->designation) — {{ $compensation->designation->name }} @endif
                    — Effective {{ \Illuminate\Support\Carbon::parse($compensation->effective_from)->format('d M Y') }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('employee_compensation_id') }}</span>
        <span class="text-danger error">{{ $errors->first('employee_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="month" class="form-label fw-semibold">Payroll Month <span class="text-danger">*</span></label>
        <select id="month" name="month" class="form-select">
            @foreach(range(1, 12) as $month)
                <option value="{{ $month }}" @selected((int) old('month', $model->month) === $month)>
                    {{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('month') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="year" class="form-label fw-semibold">Payroll Year <span class="text-danger">*</span></label>
        <input type="number" id="year" name="year" class="form-control" min="2000" max="2100"
               value="{{ old('year', $model->year) }}">
        <span class="text-danger error">{{ $errors->first('year') }}</span>
    </div>

    <div class="col-12">
        <div class="alert alert-info mb-0">
            Recalculation replaces draft advance installment records and refreshes payroll totals from the selected month.
        </div>
    </div>
</div>

<script>
    $('#employee_compensation_id').select2({
        dropdownParent: $('#employee_compensation_id').parent(),
        width: '100%'
    });

    $('#employee_compensation_id').on('change', function () {
        $('#payroll-employee-id').val(
            $('#employee_compensation_id option:selected').data('employee') || ''
        );
    });
</script>
