<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="unit_id" class="form-label fw-semibold">
          Unit
        </label>
        <select id="unit_id" name="unit_id" class="form-select">
          <option value="">Select unit</option>
          @foreach ($units as $unit)
            <option value="{{ $unit->ulid }}" {{ $model->unit_id == $unit->id ? 'selected' : '' }}>{{ ucfirst($unit->name) }}</option>
          @endforeach
        </select>
        <span id="unit_id_error" class="text-danger error">{{ $errors->first('unit_id') }}</span>
    </div>
    <!-- Name Input -->
    <div class="col-12 col-md-6">
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

    <div class="col-12 col-md-6">
        <label for="price" class="form-label fw-semibold">
            Price <span class="text-danger">*</span>
        </label>
        <input
            type="number"
            id="price"
            name="price"
            class="form-control form-control-lg"
            placeholder="Enter price"
            value="{{ old('price') }}"
        />
        <span id="price_error" class="text-danger error">{{ $errors->first('price') }}</span>
    </div>

    <div class="col-12">
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
