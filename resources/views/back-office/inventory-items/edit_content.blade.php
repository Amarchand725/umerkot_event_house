@method('PUT')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <label for="inventory_category_id" class="form-label fw-semibold">
            Inventory Category <span class="text-danger">*</span>
        </label>
        <select id="inventory_category_id" name="inventory_category_id" class="form-select">
          <option value="">Select status</option>
          @foreach ($inventoryCategories as $inventoryCategory)
            <option value="{{ $inventoryCategory->ulid }}" {{ $model->inventory_category_id == $inventoryCategory->id ? 'selected' : '' }}>{{ ucfirst($inventoryCategory->name) }}</option>
          @endforeach
        </select>
        <span id="inventory_category_id_error" class="text-danger error">{{ $errors->first('inventory_category_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="unit_id" class="form-label fw-semibold">
            Unit <span class="text-danger">*</span>
        </label>
        <select id="unit_id" name="unit_id" class="form-select">
          <option value="">Select status</option>
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
            value="{{ old('name', $model->name) }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="price_per_unit" class="form-label fw-semibold">
            Price Per Unit <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="price_per_unit"
            name="price_per_unit"
            class="form-control form-control-lg"
            placeholder="Enter price per unit"
            value="{{ old('price_per_unit', $model->price_per_unit) }}"
        />
        <span id="price_per_unit_error" class="text-danger error">{{ $errors->first('price_per_unit') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="total_quantity" class="form-label fw-semibold">
            Total Qty (Optional)
        </label>
        <input
            type="number"
            id="total_quantity"
            name="total_quantity"
            class="form-control form-control-lg"
            placeholder="Enter total quantity"
            value="{{ old('total_quantity') }}"
        />
        <span id="total_quantity_error" class="text-danger error">{{ $errors->first('total_quantity') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="minimum_quantity" class="form-label fw-semibold">
            Min Qty (Optional)
        </label>
        <input
            type="number"
            id="minimum_quantity"
            name="minimum_quantity"
            class="form-control form-control-lg"
            placeholder="Enter minimum quantity"
            value="{{ old('minimum_quantity') }}"
        />
        <span id="minimum_quantity_error" class="text-danger error">{{ $errors->first('minimum_quantity') }}</span>
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
