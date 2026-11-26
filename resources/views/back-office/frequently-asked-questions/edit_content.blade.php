
@method('PUT')

<div class="row g-3 mb-4">

    <!-- Question Input -->
    <div class="col-12">
        <label for="question" class="form-label fw-semibold">
            Question <span class="text-danger">*</span>
        </label>
        <textarea
            id="question"
            name="question"
            class="form-control form-control-lg"
            rows="3"
            placeholder="Enter frequently asked question"
        >{{ old('question', $model->question) }}</textarea>
        <span id="question_error" class="text-danger error">{{ $errors->first('question') }}</span>
    </div>

    <!-- Answer Input -->
    <div class="col-12">
        <label for="answer" class="form-label fw-semibold">
            Answer <span class="text-danger">*</span>
        </label>
        <textarea
            id="answer"
            name="answer"
            class="form-control form-control-lg"
            rows="5"
            placeholder="Enter answer"
        >{{ old('answer', $model->answer) }}</textarea>
        <span id="answer_error" class="text-danger error">{{ $errors->first('answer') }}</span>
    </div>

    <!-- Status Select -->
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>
        <select
            id="status_id"
            name="status_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->ulid }}"
                    {{ old('status_id', $model->status?->ulid) == $status->ulid ? 'selected' : '' }}>
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>

</div>

<script>
    $(document).ready(function () {
        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width: '100%'
            });
        });
    });
</script>
