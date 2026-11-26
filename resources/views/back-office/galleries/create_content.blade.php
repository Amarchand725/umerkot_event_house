
<div class="row g-3 mb-4">

    <!-- Event Select -->
    <div class="col-12 col-md-6">
        <label for="event_id" class="form-label fw-semibold">
            Event
        </label>
        <select
            id="event_id"
            name="event_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Event</option>
            @foreach($events as $event)
                <option value="{{ $event->ulid }}"
                    {{ old('event_id') == $event->ulid ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
            @endforeach
        </select>
        <span id="event_id_error" class="text-danger error">{{ $errors->first('event_id') }}</span>
    </div>

    <!-- Title Input -->
    <div class="col-12 col-md-6">
        <label for="title" class="form-label fw-semibold">
            Title
        </label>
        <input
            type="text"
            id="title"
            name="title"
            class="form-control form-control-lg"
            placeholder="Enter gallery title"
            value="{{ old('title') }}"
        />
        <span id="title_error" class="text-danger error">{{ $errors->first('title') }}</span>
    </div>

    <!-- Description -->
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Description
        </label>
        <textarea
            id="description"
            name="description"
            class="form-control form-control-lg"
            rows="4"
            placeholder="Enter gallery description"
        >{{ old('description') }}</textarea>
        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
    </div>

    <!-- Attachments -->
    <div class="col-12">
        <label for="attachments" class="form-label fw-semibold">
            Gallery Images
        </label>
        <input
            type="file"
            id="attachments"
            name="attachments[]"
            class="form-control form-control-lg"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            multiple
        />

        <small class="text-muted">
            You can upload multiple images. JPG, JPEG, PNG and WEBP (Max 2MB each).
        </small>

        <span id="attachments_error" class="text-danger error d-block">
            {{ $errors->first('attachments') }}
        </span>

        @foreach($errors->get('attachments.*') as $errorsList)
            @foreach($errorsList as $error)
                <span class="text-danger d-block">{{ $error }}</span>
            @endforeach
        @endforeach

        <!-- Image Preview -->
        <div id="image-preview" class="row g-3 mt-2"></div>
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
                    {{ old('status_id') == $status->ulid ? 'selected' : '' }}>
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>

</div>

<script>
    $(document).ready(function () {

        // Select2
        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width: '100%'
            });
        });

        // Multiple Image Preview
        $('#attachments').on('change', function (event) {
            const preview = $('#image-preview');
            preview.empty();

            Array.from(event.target.files).forEach(function (file) {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.append(`
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="border rounded p-2">
                                <img
                                    src="${e.target.result}"
                                    class="img-fluid rounded"
                                    style="height:120px;width:100%;object-fit:cover;"
                                    alt="Gallery Preview"
                                >
                                <small class="d-block text-truncate mt-2">
                                    ${file.name}
                                </small>
                            </div>
                        </div>
                    `);
                };

                reader.readAsDataURL(file);
            });
        });

    });
</script>
