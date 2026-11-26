<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="status_id" class="form-label fw-semibold">
            Status <span class="text-danger">*</span>
        </label>
        <select id="status_id" name="status_id" class="form-select">
            <option value="">Select status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->ulid }}" {{ $model?->status_id==$status->id?'selected':'' }}>{{ ucfirst($status->name) }}</option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="leave_date" class="form-label fw-semibold">
            Leave Date
        </label>
        <input
            type="text"
            id="leave_date"
            name="leave_date"
            class="form-control form-control-lg"
            value="{{ old('leave_date', isset($model->leave_date) ? \Carbon\Carbon::parse($model->leave_date)->format('Y-m-d\TH:i') : '') }}"
        />
        <span class="text-danger error">{{ $errors->first('leave_date') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="note" class="form-label fw-semibold">
            Note
        </label>
        <textarea
            id="note"
            name="note"
            class="form-control"
            rows="3"
            placeholder="Enter note"
        >{{ old('note', '') }}</textarea>
        <span id="note_error" class="text-danger error">{{ $errors->first('note') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
