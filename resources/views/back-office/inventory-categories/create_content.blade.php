<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">
            Name <span class="text-danger">*</span>
        </label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-control form-control-lg" 
            placeholder="Enter name" 
            value="{{ old('name') }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <!-- Description Input -->
    <div class="col-12"></div>
        <label for="description" class="form-label fw-semibold">
            Description
        </label>
        <textarea 
            id="description" 
            name="description" 
            class="form-control form-control-lg" 
            placeholder="Enter description"
        >{{ old('description') }}</textarea>
        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>