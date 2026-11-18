
@method('PUT')

@php
    $selectedItems = old('items', $model->items->map(fn($item) => [
        'inventory_item_id' => $item->inventory_item_id,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->toArray());

    $selectedServices = old('services', $model->services->map(fn($service) => [
        'service_id' => $service->service_id,
        'quantity' => $service->quantity,
        'unit_price' => $service->unit_price,
    ])->toArray());
@endphp

<div class="row g-3">

    {{-- Package Information --}}
    <div class="col-12">
        <h5 class="mb-0">
            <i class="ti ti-package me-1"></i>
            Package Information
        </h5>
        <hr>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Package Name <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            name="name"
            class="form-control form-control-lg"
            placeholder="Enter package name"
            value="{{ old('name', $model->name) }}"
        >
        <span id="name_error" class="text-danger error">
            {{ $errors->first('name') }}
        </span>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Description</label>
        <input
            type="text"
            name="description"
            class="form-control form-control-lg"
            placeholder="Enter package description"
            value="{{ old('description', $model->description) }}"
        >
        <span id="description_error" class="text-danger error">
            {{ $errors->first('description') }}
        </span>
    </div>

    {{-- Package Pricing --}}
    <div class="col-12 mt-4">
        <h5 class="mb-0">
            <i class="ti ti-coin me-1"></i>
            Package Pricing
        </h5>
        <hr>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Price <span class="text-danger">*</span>
        </label>
        <input
            type="number"
            name="price"
            class="form-control form-control-lg"
            min="0"
            step="0.01"
            placeholder="Enter package price"
            value="{{ old('price', $model->price) }}"
        >
        <span id="price_error" class="text-danger error">
            {{ $errors->first('price') }}
        </span>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Discount</label>
        <input
            type="number"
            name="discount"
            class="form-control form-control-lg"
            min="0"
            step="0.01"
            placeholder="Enter discount"
            value="{{ old('discount', $model->discount ?? 0) }}"
        >
        <span id="discount_error" class="text-danger error">
            {{ $errors->first('discount') }}
        </span>
    </div>

    {{-- Inventory Items --}}
    <div class="col-12 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-box me-1"></i>
                Package Items
            </h5>

            <button type="button" class="btn btn-primary btn-sm" id="add-package-item">
                <i class="ti ti-plus me-1"></i>
                Add Item
            </button>
        </div>
        <hr>
    </div>

    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="45%">Inventory Item</th>
                        <th width="20%">Quantity</th>
                        <th width="25%">Unit Price</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody id="package-items-wrapper">

                    @foreach($selectedItems as $index => $item)
                        <tr>
                            <td>
                                <select
                                    name="items[{{ $index }}][inventory_item_id]"
                                    class="form-select inventory-item-select"
                                    required
                                >
                                    <option value="">Select Inventory Item</option>

                                    @foreach($inventoryItems as $inventoryItem)
                                        <option
                                            value="{{ $inventoryItem->id }}"
                                            data-price="{{ $inventoryItem->price ?? 0 }}"
                                            @selected($item['inventory_item_id'] == $inventoryItem->id)
                                        >
                                            {{ $inventoryItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="items[{{ $index }}][quantity]"
                                    class="form-control"
                                    min="1"
                                    value="{{ $item['quantity'] }}"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="items[{{ $index }}][unit_price]"
                                    class="form-control item-unit-price"
                                    min="0"
                                    step="0.01"
                                    value="{{ $item['unit_price'] }}"
                                    required
                                >
                            </td>

                            <td>
                                <button type="button" class="btn btn-icon btn-label-danger remove-package-item">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <span id="items_error" class="text-danger error">
            {{ $errors->first('items') }}
        </span>
    </div>

    {{-- Package Services --}}
    <div class="col-12 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-tools me-1"></i>
                Package Services
            </h5>

            <button type="button" class="btn btn-primary btn-sm" id="add-package-service">
                <i class="ti ti-plus me-1"></i>
                Add Service
            </button>
        </div>
        <hr>
    </div>

    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="45%">Service</th>
                        <th width="20%">Quantity</th>
                        <th width="25%">Unit Price</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody id="package-services-wrapper">

                    @foreach($selectedServices as $index => $service)
                        <tr>
                            <td>
                                <select
                                    name="services[{{ $index }}][service_id]"
                                    class="form-select package-service-select"
                                    required
                                >
                                    <option value="">Select Service</option>

                                    @foreach($inventoryServices as $inventoryService)
                                        <option
                                            value="{{ $inventoryService->id }}"
                                            data-price="{{ $inventoryService->price ?? 0 }}"
                                            @selected($service['service_id'] == $inventoryService->id)
                                        >
                                            {{ $inventoryService->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="services[{{ $index }}][quantity]"
                                    class="form-control"
                                    min="1"
                                    value="{{ $service['quantity'] }}"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="services[{{ $index }}][unit_price]"
                                    class="form-control service-unit-price"
                                    min="0"
                                    step="0.01"
                                    value="{{ $service['unit_price'] }}"
                                    required
                                >
                            </td>

                            <td>
                                <button type="button" class="btn btn-icon btn-label-danger remove-package-service">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <span id="services_error" class="text-danger error">
            {{ $errors->first('services') }}
        </span>
    </div>

    {{-- Status --}}
    <div class="col-12 mt-4">
        <h5 class="mb-0">
            <i class="ti ti-toggle-right me-1"></i>
            Package Status
        </h5>
        <hr>
    </div>

    <div class="col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>

        <select id="status_id" name="status_id" class="form-select">
            <option value="">Select Status</option>

            @foreach($statuses as $status)
                <option
                    value="{{ $status->ulid }}"
                    @selected(old('status_id', $model->status?->ulid) == $status->ulid)
                >
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>

        <span id="status_id_error" class="text-danger error">
            {{ $errors->first('status_id') }}
        </span>
    </div>

</div>

{{-- Inventory Item Template --}}
<template id="package-item-template">
    <tr>
        <td>
            <select class="form-select inventory-item-select" required>
                <option value="">Select Inventory Item</option>

                @foreach($inventoryItems as $item)
                    <option
                        value="{{ $item->id }}"
                        data-price="{{ $item->price ?? 0 }}"
                    >
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number" class="form-control" min="1" value="1" required>
        </td>

        <td>
            <input type="number" class="form-control item-unit-price"
                   min="0" step="0.01" value="0" required>
        </td>

        <td>
            <button type="button" class="btn btn-icon btn-label-danger remove-package-item">
                <i class="ti ti-trash"></i>
            </button>
        </td>
    </tr>
</template>

{{-- Service Template --}}
<template id="package-service-template">
    <tr>
        <td>
            <select class="form-select package-service-select" required>
                <option value="">Select Service</option>

                @foreach($inventoryServices as $service)
                    <option
                        value="{{ $service->id }}"
                        data-price="{{ $service->price ?? 0 }}"
                    >
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number" class="form-control" min="1" value="1" required>
        </td>

        <td>
            <input type="number" class="form-control service-unit-price"
                   min="0" step="0.01" value="0" required>
        </td>

        <td>
            <button type="button" class="btn btn-icon btn-label-danger remove-package-service">
                <i class="ti ti-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    let packageItemIndex = {{ count($selectedItems) }};
    let packageServiceIndex = {{ count($selectedServices) }};

    function initializePackageSelect(element) {
        $(element).select2({
            dropdownParent: $(element).parent(),
            width: '100%'
        });
    }

    // Initialize existing Select2 elements
    $('.inventory-item-select, .package-service-select, #status_id').each(function () {
        initializePackageSelect(this);
    });

    // Add Inventory Item
    $('#add-package-item').on('click', function () {

        let row = $($('#package-item-template').html());

        row.find('select').attr(
            'name',
            `items[${packageItemIndex}][inventory_item_id]`
        );

        row.find('input').eq(0).attr(
            'name',
            `items[${packageItemIndex}][quantity]`
        );

        row.find('input').eq(1).attr(
            'name',
            `items[${packageItemIndex}][unit_price]`
        );

        packageItemIndex++;

        $('#package-items-wrapper').append(row);

        initializePackageSelect(row.find('select'));
    });

    // Add Service
    $('#add-package-service').on('click', function () {

        let row = $($('#package-service-template').html());

        row.find('select').attr(
            'name',
            `services[${packageServiceIndex}][service_id]`
        );

        row.find('input').eq(0).attr(
            'name',
            `services[${packageServiceIndex}][quantity]`
        );

        row.find('input').eq(1).attr(
            'name',
            `services[${packageServiceIndex}][unit_price]`
        );

        packageServiceIndex++;

        $('#package-services-wrapper').append(row);

        initializePackageSelect(row.find('select'));
    });

    // Auto-fill inventory item price
    $(document).on('change', '.inventory-item-select', function () {

        let price = $(this).find(':selected').data('price') || 0;

        $(this).closest('tr')
            .find('.item-unit-price')
            .val(price);
    });

    // Auto-fill service price
    $(document).on('change', '.package-service-select', function () {

        let price = $(this).find(':selected').data('price') || 0;

        $(this).closest('tr')
            .find('.service-unit-price')
            .val(price);
    });

    // Remove Inventory Item
    $(document).on('click', '.remove-package-item', function () {
        $(this).closest('tr').remove();
    });

    // Remove Service
    $(document).on('click', '.remove-package-service', function () {
        $(this).closest('tr').remove();
    });
</script>