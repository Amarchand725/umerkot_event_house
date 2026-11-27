<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <label for="payroll_id" class="form-label fw-semibold">Payroll <span class="text-danger">*</span></label>
        <select id="payroll_id" name="payroll_id" class="form-select select2" required>
            <option value="">Select Payroll</option>
            @foreach($payrolls as $payroll)
                @php
                    $outstanding = max(0, (float) $payroll->net_payable - (float) ($payroll->payments_sum_amount ?? 0));
                    if ($model && (int) $model->payroll_id === (int) $payroll->id) {
                        $outstanding += (float) $model->amount;
                    }
                @endphp
                <option value="{{ $payroll->ulid }}"
                    data-outstanding="{{ number_format($outstanding, 2, '.', '') }}"
                    @selected(old('payroll_id', $model?->payroll?->ulid) == $payroll->ulid)>
                    {{ $payroll->employee?->name ?? 'Unknown employee' }}
                    — {{ \Illuminate\Support\Carbon::create((int) $payroll->year, (int) $payroll->month, 1)->format('F Y') }}
                    — Due Rs. {{ number_format($outstanding, 2) }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('payroll_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="amount" class="form-label fw-semibold">Amount (PKR) <span class="text-danger">*</span></label>
        <input type="number" id="amount" name="amount" min="0.01" step="0.01" class="form-control" required
               value="{{ old('amount', $model?->amount) }}">
        <small class="text-muted" id="payroll-balance-hint"></small>
        <span class="text-danger error d-block">{{ $errors->first('amount') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="payment_method_id" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
        <select id="payment_method_id" name="payment_method_id" class="form-select select2" required>
            <option value="">Select Payment Method</option>
            @foreach($paymentMethods as $method)
                <option value="{{ $method->ulid }}"
                    @selected(old('payment_method_id', $model?->paymentMethod?->ulid) == $method->ulid)>
                    {{ $method->name }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('payment_method_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="payment_date" class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
        <input type="date" id="payment_date" name="payment_date" class="form-control" required
               value="{{ old('payment_date', $model?->payment_date ? \Illuminate\Support\Carbon::parse($model->payment_date)->format('Y-m-d') : now()->toDateString()) }}">
        <span class="text-danger error">{{ $errors->first('payment_date') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">Payment Status</label>
        <select id="status_id" name="status_id" class="form-select select2">
            <option value="">Select Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->ulid }}"
                    @selected(old('status_id', $model?->status?->ulid ?? $defaultPaymentStatus) == $status->ulid)>
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="reference_number" class="form-label fw-semibold">Reference Number</label>
        <input type="text" id="reference_number" name="reference_number" maxlength="255" class="form-control"
               value="{{ old('reference_number', $model?->reference_number) }}">
        <span class="text-danger error">{{ $errors->first('reference_number') }}</span>
    </div>

    <div class="col-12">
        <label for="notes" class="form-label fw-semibold">Notes</label>
        <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes', $model?->notes) }}</textarea>
        <span class="text-danger error">{{ $errors->first('notes') }}</span>
    </div>
</div>

<script>
    $('.select2').each(function () {
        $(this).select2({ dropdownParent: $(this).parent(), width: '100%' });
    });

    function updatePayrollBalance() {
        const outstanding = Number($('#payroll_id option:selected').data('outstanding') || 0);
        $('#amount').attr('max', outstanding > 0 ? outstanding : null);
        $('#payroll-balance-hint').text(outstanding > 0
            ? 'Outstanding payroll balance: Rs. ' + outstanding.toFixed(2)
            : 'Select a payroll with an outstanding balance.');
    }

    $('#payroll_id').on('change', updatePayrollBalance);
    updatePayrollBalance();
</script>
