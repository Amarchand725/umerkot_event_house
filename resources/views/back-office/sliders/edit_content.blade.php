
@method('PUT')

<div class="row g-3 mb-4">

    {{-- Title --}}
    <div class="col-12 col-md-6">
        <label for="title" class="form-label fw-semibold">
            Title
        </label>
        <input
            type="text"
            id="title"
            name="title"
            class="form-control form-control-lg"
            placeholder="Enter slider title"
            value="{{ old('title', $model->title) }}"
        />
        <span id="title_error" class="text-danger error">
            {{ $errors->first('title') }}
        </span>
    </div>

    {{-- Status --}}
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
            @foreach ($statuses as $status)
                <option
                    value="{{ $status->ulid }}"
                    {{ old('status_id', $model->status?->ulid) == $status->ulid ? 'selected' : '' }}
                >
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">
            {{ $errors->first('status_id') }}
        </span>
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Description
        </label>
        <textarea
            id="description"
            name="description"
            class="form-control"
            rows="4"
            placeholder="Enter slider description"
        >{{ old('description', $model->description) }}</textarea>
        <span id="description_error" class="text-danger error">
            {{ $errors->first('description') }}
        </span>
    </div>

    {{-- Slider Image --}}
    <div class="col-12">
        <label for="attachment" class="form-label fw-semibold">
            Slider Image
        </label>

        <input
            type="file"
            id="attachment"
            name="attachment"
            class="form-control form-control-lg"
            accept="image/jpeg,image/png,image/webp"
        />

        <span class="form-text">
            Upload a new image to replace the existing one.
        </span>

        <span id="attachment_error" class="text-danger error">
            {{ $errors->first('attachment') }}
        </span>

        {{-- Existing / New Image Preview --}}
        <div class="mt-3" id="image_preview_container"
             style="{{ $model->attachment ? '' : 'display: none;' }}">

            <label class="form-label fw-semibold">Image Preview</label>

            <div>
                <img
                    id="image_preview"
                    src="{{ $model->attachment?->path }}"
                    alt="Slider Preview"
                    class="img-thumbnail"
                    style="max-width: 350px; max-height: 200px; object-fit: cover;"
                />
            </div>

            <button
                type="button"
                class="btn btn-sm btn-danger mt-2"
                id="remove_image"
            >
                <i class="ti ti-trash me-1"></i>
                Remove Preview
            </button>
        </div>
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

        // New image preview
        $('#attachment').on('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image.');
                    $(this).val('');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    $('#image_preview').attr('src', event.target.result);
                    $('#image_preview_container').show();
                };

                reader.readAsDataURL(file);
            }
        });

        // Clear preview
        $('#remove_image').on('click', function () {
            $('#attachment').val('');
            $('#image_preview').attr('src', '');
            $('#image_preview_container').hide();
        });

    });
</script>
