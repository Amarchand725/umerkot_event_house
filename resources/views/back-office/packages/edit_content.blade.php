@method('PUT')
<div class="row g-3 mb-4">
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
            value="{{ old('name', $model->name) }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="price" class="form-label fw-semibold">
            Price <span class="text-danger">*</span>
        </label>
        <input
            type="number"
            id="price"
            name="price"
            class="form-control form-control-lg"
            placeholder="Enter price per unit"
            value="{{ old('price', $model->price) }}"
        />
        <span id="price_error" class="text-danger error">{{ $errors->first('price') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="discount" class="form-label fw-semibold">
            Discount <span class="text-danger">*</span>
        </label>
        <input
            type="number"
            id="discount"
            name="discount"
            class="form-control form-control-lg"
            placeholder="Enter discount per unit"
            value="{{ old('discount', $model->discount) }}"
        />
        <span id="discount_error" class="text-danger error">{{ $errors->first('discount') }}</span>
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
        >{{ old('description', $model->description) }}</textarea>
        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="status_id" class="form-label fw-semibold">
          Status
        </label>
        <select id="status_id" name="status_id" class="form-select">
          <option value="">Select status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->ulid }}" {{ $model->status_id == $status->id ? 'selected' : '' }}>{{ ucfirst($status->name) }}</option>
          @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
