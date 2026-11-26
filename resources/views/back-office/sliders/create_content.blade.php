
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
            value="{{ old('title') }}"
        />
        <span id="title_error" class="text-danger error">
            {{ $errors->first('title') }}
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
        >{{ old('description') }}</textarea>
        <span id="description_error" class="text-danger error">
            {{ $errors->first('description') }}
        </span>
    </div>

    {{-- Attachment --}}
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
            Upload a slider image (JPG, PNG, WEBP).
        </span>

        <span id="attachment_error" class="text-danger error">
            {{ $errors->first('attachment') }}
        </span>

        {{-- Image Preview --}}
        <div class="mt-3" id="image_preview_container" style="display: none;">
            <label class="form-label fw-semibold">Image Preview</label>
            <div>
                <img
                    id="image_preview"
                    src=""
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
                <i class="ti ti-trash me-1"></i> Remove Image
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
                    $('#image_preview')
                        .attr('src', event.target.result);

                    $('#image_preview_container').show();
                };

                reader.readAsDataURL(file);
            }
        });

        // Remove selected image
        $('#remove_image').on('click', function () {
            $('#attachment').val('');
            $('#image_preview').attr('src', '');
            $('#image_preview_container').hide();
        });
    });
</script>
