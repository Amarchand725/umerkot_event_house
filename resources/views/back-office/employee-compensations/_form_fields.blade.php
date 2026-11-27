@php
    $currentEmployee = $model?->employee;
    $dateValue = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') : '';
@endphp

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <label for="employee_id" class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
        <select id="employee_id" name="employee_id" class="form-select select2" required>
            <option value="">Select Employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->ulid }}"
                    @selected(old('employee_id', $currentEmployee?->ulid) == $employee->ulid)>
                    {{ $employee->name }}
                    @if($employee->joining_date)
                        — Joined {{ \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d M Y') }}
                    @endif
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('employee_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="designation_id" class="form-label fw-semibold">Designation</label>
        <select id="designation_id" name="designation_id" class="form-select select2">
            <option value="">Select Designation</option>
            @foreach($designations as $designation)
                <option value="{{ $designation->ulid }}"
                    @selected(old('designation_id', $model?->designation?->ulid) == $designation->ulid)>
                    {{ $designation->name }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('designation_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="pay_type" class="form-label fw-semibold">Salary Type <span class="text-danger">*</span></label>
        <select id="pay_type" name="pay_type" class="form-select" required>
            <option value="">Select Salary Type</option>
            @foreach($payTypes as $type)
                <option value="{{ $type->value }}"
                    @selected(old('pay_type', $model?->pay_type) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('pay_type') }}</span>
    </div>

    <div class="col-12 col-md-6" id="basic_pay_wrapper">
        <label for="basic_pay" class="form-label fw-semibold">Monthly Base Salary (PKR)</label>
        <input type="number" step="0.01" min="0" id="basic_pay" name="basic_pay" class="form-control"
               value="{{ old('basic_pay', $model?->basic_pay) }}">
        <span class="text-danger error">{{ $errors->first('basic_pay') }}</span>
    </div>

    <div class="col-12 col-md-6" id="daily_rate_wrapper">
        <label for="daily_rate" class="form-label fw-semibold">Daily Rate (PKR)</label>
        <input type="number" step="0.01" min="0" id="daily_rate" name="daily_rate" class="form-control"
               value="{{ old('daily_rate', $model?->daily_rate) }}">
        <span class="text-danger error">{{ $errors->first('daily_rate') }}</span>
    </div>

    <div class="col-12 col-md-6" id="hourly_rate_wrapper">
        <label for="hourly_rate" class="form-label fw-semibold">Hourly Rate (PKR)</label>
        <input type="number" step="0.01" min="0" id="hourly_rate" name="hourly_rate" class="form-control"
               value="{{ old('hourly_rate', $model?->hourly_rate) }}">
        <span class="text-danger error">{{ $errors->first('hourly_rate') }}</span>
    </div>

    <div class="col-12 col-md-6" id="commission_basis_wrapper">
        <label for="commission_basis" class="form-label fw-semibold">Commission Basis</label>
        <select id="commission_basis" name="commission_basis" class="form-select">
            <option value="">Select Basis</option>
            @foreach($commissionBasises as $basis)
                <option value="{{ $basis->value }}"
                    @selected(old('commission_basis', $model?->commission_basis) === $basis->value)>
                    {{ $basis->label() }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('commission_basis') }}</span>
    </div>

    <div class="col-12 col-md-6" id="commission_type_wrapper">
        <label for="commission_type" class="form-label fw-semibold">Commission Type</label>
        <select id="commission_type" name="commission_type" class="form-select">
            <option value="">Select Commission Type</option>
            @foreach($commissionTypes as $type)
                <option value="{{ $type->value }}"
                    @selected(old('commission_type', $model?->commission_type) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('commission_type') }}</span>
    </div>

    <div class="col-12 col-md-6" id="commission_rate_wrapper">
        <label for="commission_rate" class="form-label fw-semibold">Commission Rate / Amount</label>
        <input type="number" step="0.01" min="0" id="commission_rate" name="commission_rate" class="form-control"
               value="{{ old('commission_rate', $model?->commission_rate) }}">
        <span class="text-danger error">{{ $errors->first('commission_rate') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="effective_from" class="form-label fw-semibold">Effective From <span class="text-danger">*</span></label>
        <input type="date" id="effective_from" name="effective_from" class="form-control" required
               value="{{ old('effective_from', $dateValue($model?->effective_from) ?: now()->toDateString()) }}">
        <span class="text-danger error">{{ $errors->first('effective_from') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="effective_to" class="form-label fw-semibold">Effective To</label>
        <input type="date" id="effective_to" name="effective_to" class="form-control"
               value="{{ old('effective_to', $dateValue($model?->effective_to)) }}">
        <span class="text-danger error">{{ $errors->first('effective_to') }}</span>
    </div>

    @if($model)
        <div class="col-12 col-md-6">
            <label for="status_id" class="form-label fw-semibold">Status</label>
            <select id="status_id" name="status_id" class="form-select">
                @foreach($statuses as $status)
                    <option value="{{ $status->ulid }}"
                        @selected(old('status_id', $model->status_id) == $status->id)>
                        {{ ucfirst($status->name) }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger error">{{ $errors->first('status_id') }}</span>
        </div>
    @endif

    <div class="col-12">
        <label for="note" class="form-label fw-semibold">Notes</label>
        <textarea id="note" name="note" rows="3" class="form-control">{{ old('note', $model?->note) }}</textarea>
        <span class="text-danger error">{{ $errors->first('note') }}</span>
    </div>
</div>

<script>
    $('.select2').each(function () {
        $(this).select2({ dropdownParent: $(this).parent(), width: '100%' });
    });

    function updateCompensationFields() {
        const payType = $('#pay_type').val();
        $('#basic_pay_wrapper').toggle(payType === 'monthly' || payType === 'hybrid');
        $('#daily_rate_wrapper').toggle(payType === 'daily');
        $('#hourly_rate_wrapper').toggle(payType === 'hourly');
        const hasCommission = payType === 'commission' || payType === 'hybrid';
        $('#commission_basis_wrapper, #commission_type_wrapper, #commission_rate_wrapper').toggle(hasCommission);
    }

    $('#pay_type').on('change', updateCompensationFields);
    updateCompensationFields();
</script>
