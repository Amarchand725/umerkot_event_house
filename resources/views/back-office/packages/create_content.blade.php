
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
            value="{{ old('name') }}"
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
            value="{{ old('description') }}"
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

    {{-- Original Price --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Original Price <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="price"
            id="package-original-price"
            class="form-control form-control-lg"
            placeholder="0.00"
            min="0"
            step="0.01"
            value="{{ old('price', 0) }}"
            readonly
        >

        <small class="text-muted">
            Automatically calculated from inventory items and services.
        </small>

        <span id="price_error" class="text-danger error">
            {{ $errors->first('price') }}
        </span>
    </div>

    {{-- Discount --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Discount
        </label>

        <input
            type="number"
            name="discount"
            id="package-discount"
            class="form-control form-control-lg"
            placeholder="Enter discount"
            min="0"
            step="0.01"
            value="{{ old('discount', 0) }}"
        >

        <span id="discount_error" class="text-danger error">
            {{ $errors->first('discount') }}
        </span>
    </div>

    {{-- Final Price --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Final Package Price
        </label>

        <input
            type="number"
            id="package-final-price"
            class="form-control form-control-lg"
            placeholder="0.00"
            min="0"
            step="0.01"
            value="{{ old('price', 0) }}"
            readonly
        >

        <small class="text-success fw-semibold">
            Final amount after discount.
        </small>
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
                <tbody id="package-items-wrapper"></tbody>
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
                <tbody id="package-services-wrapper"></tbody>
            </table>
        </div>

        <span id="services_error" class="text-danger error">
            {{ $errors->first('services') }}
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
                        value="{{ $item->ulid }}"
                        data-price="{{ $item->price_per_unit ?? 0 }}"
                    >
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number" class="form-control"
                   min="1" value="1" required>
        </td>

        <td>
            <input type="number" class="form-control item-unit-price"
                   min="0" step="0.01" value="0" required>
        </td>

        <td>
            <button type="button"
                    class="btn btn-icon btn-label-danger remove-package-item">
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
                        value="{{ $service->ulid }}"
                        data-price="{{ $service->price ?? 0 }}"
                    >
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number" class="form-control"
                   min="1" value="1" required>
        </td>

        <td>
            <input type="number" class="form-control service-unit-price"
                   min="0" step="0.01" value="0" required>
        </td>

        <td>
            <button type="button"
                    class="btn btn-icon btn-label-danger remove-package-service">
                <i class="ti ti-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    let packageItemIndex = 0;
    let packageServiceIndex = 0;

    // Initialize Select2
    function initializePackageSelect(element) {
        $(element).select2({
            dropdownParent: $(element).parent(),
            width: '100%'
        });
    }

    // Calculate package pricing
    function calculatePackagePrice() {

        let originalPrice = 0;

        // Inventory items
        $('#package-items-wrapper tr').each(function () {

            let quantity = parseFloat(
                $(this).find('input[name*="[quantity]"]').val()
            ) || 0;

            let unitPrice = parseFloat(
                $(this).find('.item-unit-price').val()
            ) || 0;

            originalPrice += quantity * unitPrice;
        });

        // Services
        $('#package-services-wrapper tr').each(function () {

            let quantity = parseFloat(
                $(this).find('input[name*="[quantity]"]').val()
            ) || 0;

            let unitPrice = parseFloat(
                $(this).find('.service-unit-price').val()
            ) || 0;

            originalPrice += quantity * unitPrice;
        });

        // Discount
        let discount = parseFloat(
            $('#package-discount').val()
        ) || 0;

        // Discount cannot exceed original price
        if (discount > originalPrice) {
            discount = originalPrice;
            $('#package-discount').val(discount);
        }

        // Final amount
        let finalPrice = originalPrice - discount;

        if (finalPrice < 0) {
            finalPrice = 0;
        }

        // Update fields
        $('#package-original-price').val(originalPrice.toFixed(2));
        $('#package-final-price').val(finalPrice.toFixed(2));
    }


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

        calculatePackagePrice();
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

        calculatePackagePrice();
    });


    // Inventory item selected
    $(document).on('change', '.inventory-item-select', function () {

        let price = parseFloat(
            $(this).find(':selected').data('price')
        ) || 0;

        $(this)
            .closest('tr')
            .find('.item-unit-price')
            .val(price);

        calculatePackagePrice();
    });


    // Service selected
    $(document).on('change', '.package-service-select', function () {

        let price = parseFloat(
            $(this).find(':selected').data('price')
        ) || 0;

        $(this)
            .closest('tr')
            .find('.service-unit-price')
            .val(price);

        calculatePackagePrice();
    });


    // Quantity or unit price changed
    $(document).on(
        'input',
        '#package-items-wrapper input, #package-services-wrapper input',
        function () {
            calculatePackagePrice();
        }
    );


    // Discount changed
    $(document).on('input', '#package-discount', function () {
        calculatePackagePrice();
    });


    // Remove inventory item
    $(document).on('click', '.remove-package-item', function () {

        $(this).closest('tr').remove();

        calculatePackagePrice();
    });


    // Remove service
    $(document).on('click', '.remove-package-service', function () {

        $(this).closest('tr').remove();

        calculatePackagePrice();
    });


    // Initialize default rows
    $('#add-package-item').trigger('click');
    $('#add-package-service').trigger('click');

    // Initial calculation
    calculatePackagePrice();
</script>