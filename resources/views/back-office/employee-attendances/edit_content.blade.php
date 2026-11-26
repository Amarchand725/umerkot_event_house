
@method('PUT')

<div class="row g-3 mb-4">

    {{-- Employee --}}
    <div class="col-12 col-md-12">
        <label for="employee_id" class="form-label fw-semibold">
            Employee <span class="text-danger">*</span>
        </label>
        <select
            id="employee_id"
            name="employee_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Employee</option>
            @foreach ($employees as $employee)
                <option
                    value="{{ $employee->ulid }}"
                    {{ old('employee_id', $model->employee?->ulid ?? '') == $employee->ulid ? 'selected' : '' }}
                >
                    {{ $employee->name }}
                    @if($employee?->compensation?->designation)
                        ({{ $employee->compensation->designation->name }})
                    @endif
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('employee_id') }}</span>
    </div>

    {{-- Attendance Status --}}
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Attendance Status <span class="text-danger">*</span>
        </label>
        <select
            id="status_id"
            name="status_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Status</option>
            @foreach ($statuses as $status)
                <option
                    value="{{ $status->ulid }}"
                    {{ old('status_id', $model->status?->ulid ?? '') == $status->ulid ? 'selected' : '' }}
                >
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>

    {{-- Payable Units --}}
    <div class="col-12 col-md-6">
        <label for="payable_units" class="form-label fw-semibold">
            Payable Units <span class="text-danger">*</span>
        </label>
        <select
            id="payable_units"
            name="payable_units"
            class="form-select form-select-lg"
        >
            <option value="1" {{ old('payable_units', $model->payable_units ?? 1) == 1 ? 'selected' : '' }}>
                Full Day (1.0)
            </option>
            <option value="0.5" {{ old('payable_units', $model->payable_units ?? 1) == 1 ? '' : (old('payable_units', $model->payable_units ?? 1) == 0.5 ? 'selected' : '') }}>
                Half Day (0.5)
            </option>
            <option value="0" {{ old('payable_units', $model->payable_units ?? 1) == 0 ? 'selected' : '' }}>
                Unpaid (0)
            </option>
        </select>
        <span class="text-danger error">{{ $errors->first('payable_units') }}</span>
    </div>

    {{-- Worked Hours --}}
    <div class="col-12 col-md-6">
        <label for="worked_hours" class="form-label fw-semibold">
            Worked Hours
        </label>
        <input
            type="number"
            id="worked_hours"
            name="worked_hours"
            class="form-control form-control-lg"
            placeholder="Enter worked hours"
            min="0"
            max="9999.99"
            step="0.01"
            value="{{ old('worked_hours', $model->worked_hours ?? 0) }}"
        />
        <span class="text-danger error">{{ $errors->first('worked_hours') }}</span>
    </div>

    {{-- Overtime Hours --}}
    <div class="col-12 col-md-6">
        <label for="overtime_hours" class="form-label fw-semibold">
            Overtime Hours
        </label>
        <input
            type="number"
            id="overtime_hours"
            name="overtime_hours"
            class="form-control form-control-lg"
            placeholder="Enter overtime hours"
            min="0"
            max="9999.99"
            step="0.01"
            value="{{ old('overtime_hours', $model->overtime_hours ?? 0) }}"
        />
        <span class="text-danger error">{{ $errors->first('overtime_hours') }}</span>
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label for="notes" class="form-label fw-semibold">
            Notes
        </label>
        <textarea
            id="notes"
            name="notes"
            class="form-control"
            rows="3"
            placeholder="Enter attendance notes"
        >{{ old('notes', $model->notes ?? '') }}</textarea>
        <span class="text-danger error">{{ $errors->first('notes') }}</span>
    </div>

</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
            width: '100%',
        });
    });
</script>
