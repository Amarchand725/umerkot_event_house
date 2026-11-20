@method('PUT')

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
            value="{{ old('name', $package->name) }}"
        >

        <span id="name_error" class="text-danger error">
            {{ $errors->first('name') }}
        </span>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Description
        </label>

        <input
            type="text"
            name="description"
            class="form-control form-control-lg"
            placeholder="Enter package description"
            value="{{ old('description', $package->description) }}"
        >

        <span id="description_error" class="text-danger error">
            {{ $errors->first('description') }}
        </span>
    </div>

    <div class="col-12">

        <label
            for="status_id"
            class="form-label fw-semibold"
        >
            Status <span class="text-danger">*</span>
        </label>

        <select
            id="status_id"
            name="status_id"
            class="form-select form-select-lg"
        >

            <option value="">
                Select Status
            </option>

            @foreach($statuses as $status)

                <option
                    value="{{ $status->ulid }}"
                    @selected(
                        old(
                            'status_id',
                            $model?->status_id
                        ) == $status->id
                    )
                >
                    {{ ucfirst($status->name) }}
                </option>

            @endforeach

        </select>

        <span class="text-danger error">
            {{ $errors->first('status_id') }}
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
            value="{{ old('price', $package->price ?? 0) }}"
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
            value="{{ old('discount', $package->discount ?? 0) }}"
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
            value="{{ $package->price-$package->discount }}"
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

            <button
                type="button"
                class="btn btn-primary btn-sm"
                id="add-package-item"
            >
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

                    @foreach($model->inventoryItems as $index => $packageItem)
                        @if($packageItem->inventory_item_id)
                            @php
                                $inventoryItem = $packageItem;

                                $quantity = $packageItem->quantity ?? 1;

                                $unitPrice = $packageItem->unit_price ?? 0;
                            @endphp

                            <tr>

                                <td>
                                    <select
                                        name="items[{{ $index }}][inventory_item_id]"
                                        class="form-select inventory-item-select"
                                        required
                                    >

                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                        @foreach($inventoryItems as $item)

                                            <option
                                                value="{{ $item->ulid }}"
                                                data-price="{{ $item->price_per_unit ?? 0 }}"
                                                {{ $inventoryItem->id == $item->id ? 'selected' : '' }}
                                            >
                                                {{ $item->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="items[{{ $index }}][quantity]"
                                        class="form-control item-quantity"
                                        min="1"
                                        value="{{ $quantity }}"
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
                                        value="{{ $unitPrice }}"
                                        required
                                    >
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-icon btn-label-danger remove-package-item"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>

                            </tr>
                        @endif

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

            <button
                type="button"
                class="btn btn-primary btn-sm"
                id="add-package-service"
            >
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

                    @foreach($model->inventoryItems as $index => $packageService)
                        @if($packageService->service_id)
                            @php
                                $service = $packageService;

                                $quantity = $packageService->pivot->quantity
                                    ?? $packageService->quantity
                                    ?? 1;

                                $unitPrice = $packageService->pivot->unit_price
                                    ?? $packageService->unit_price
                                    ?? $service->price
                                    ?? 0;
                            @endphp

                            <tr>

                                <td>

                                    <select
                                        name="services[{{ $index }}][service_id]"
                                        class="form-select package-service-select"
                                        required
                                    >

                                        <option value="">
                                            Select Service
                                        </option>

                                        @foreach($inventoryServices as $itemService)

                                            <option
                                                value="{{ $itemService->ulid }}"
                                                data-price="{{ $itemService->price ?? 0 }}"
                                                {{ $service->service_id == $itemService->id ? 'selected' : '' }}
                                            >
                                                {{ $itemService->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td>

                                    <input
                                        type="number"
                                        name="services[{{ $index }}][quantity]"
                                        class="form-control service-quantity"
                                        min="1"
                                        value="{{ $quantity }}"
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
                                        value="{{ $unitPrice }}"
                                        required
                                    >

                                </td>

                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-icon btn-label-danger remove-package-service"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>

                                </td>

                            </tr>
                        @endif

                    @endforeach

                </tbody>

            </table>

        </div>

        <span id="services_error" class="text-danger error">
            {{ $errors->first('services') }}
        </span>

    </div>

</div>

</div>

{{-- Inventory Item Template --}}
{{-- Inventory Item Template --}}
<template id="package-item-template">
    <tr>

        <td>
            <select class="form-select inventory-item-select" required>

                <option value="">
                    Select Inventory Item
                </option>

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
            <input
                type="number"
                class="form-control item-quantity"
                min="1"
                value="1"
                required
            >
        </td>

        <td>
            <input
                type="number"
                class="form-control item-unit-price"
                min="0"
                step="0.01"
                value="0"
                required
            >
        </td>

        <td>
            <button
                type="button"
                class="btn btn-icon btn-label-danger remove-package-item"
            >
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

                <option value="">
                    Select Service
                </option>

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
            <input
                type="number"
                class="form-control service-quantity"
                min="1"
                value="1"
                required
            >
        </td>

        <td>
            <input
                type="number"
                class="form-control service-unit-price"
                min="0"
                step="0.01"
                value="0"
                required
            >
        </td>

        <td>
            <button
                type="button"
                class="btn btn-icon btn-label-danger remove-package-service"
            >
                <i class="ti ti-trash"></i>
            </button>
        </td>

    </tr>
</template>

<script>
    let packageItemIndex = {{ $model->inventoryItems->count() }};
    let packageServiceIndex = {{ $model->inventoryItems->count() }};

    // --------------------------------------------------
    // Initialize Select2
    // --------------------------------------------------

    function initializePackageSelect(element) {

        $(element).select2({
            dropdownParent: $(element).parent(),
            width: '100%'
        });

    }


    // --------------------------------------------------
    // Calculate Package Price
    // --------------------------------------------------

    function calculatePackagePrice() {

        let originalPrice = 0;


        // ----------------------------------------------
        // Inventory Items
        // ----------------------------------------------

        $('#package-items-wrapper tr').each(function () {

            let quantity = parseFloat(
                $(this).find('.item-quantity').val()
            ) || 0;

            let unitPrice = parseFloat(
                $(this).find('.item-unit-price').val()
            ) || 0;

            originalPrice += quantity * unitPrice;

        });


        // ----------------------------------------------
        // Services
        // ----------------------------------------------

        $('#package-services-wrapper tr').each(function () {

            let quantity = parseFloat(
                $(this).find('.service-quantity').val()
            ) || 0;

            let unitPrice = parseFloat(
                $(this).find('.service-unit-price').val()
            ) || 0;

            originalPrice += quantity * unitPrice;

        });


        // ----------------------------------------------
        // Discount
        // ----------------------------------------------

        let discount = parseFloat(
            $('#package-discount').val()
        ) || 0;


        // Discount cannot exceed original price
        if (discount > originalPrice) {

            discount = originalPrice;

            $('#package-discount').val(
                discount.toFixed(2)
            );

        }


        // ----------------------------------------------
        // Final Price
        // ----------------------------------------------

        let finalPrice = originalPrice - discount;


        if (finalPrice < 0) {
            finalPrice = 0;
        }


        // ----------------------------------------------
        // Update Fields
        // ----------------------------------------------

        $('#package-original-price').val(
            originalPrice.toFixed(2)
        );

        $('#package-final-price').val(
            finalPrice.toFixed(2)
        );

    }


    // --------------------------------------------------
    // Add Inventory Item
    // --------------------------------------------------

    $('#add-package-item').on('click', function () {

        let row = $(
            $('#package-item-template').html()
        );


        row.find('select').attr(
            'name',
            `items[${packageItemIndex}][inventory_item_id]`
        );


        row.find('.item-quantity').attr(
            'name',
            `items[${packageItemIndex}][quantity]`
        );


        row.find('.item-unit-price').attr(
            'name',
            `items[${packageItemIndex}][unit_price]`
        );


        packageItemIndex++;


        $('#package-items-wrapper').append(row);


        initializePackageSelect(
            row.find('select')
        );


        calculatePackagePrice();

    });


    // --------------------------------------------------
    // Add Service
    // --------------------------------------------------

    $('#add-package-service').on('click', function () {

        let row = $(
            $('#package-service-template').html()
        );


        row.find('select').attr(
            'name',
            `services[${packageServiceIndex}][service_id]`
        );


        row.find('.service-quantity').attr(
            'name',
            `services[${packageServiceIndex}][quantity]`
        );


        row.find('.service-unit-price').attr(
            'name',
            `services[${packageServiceIndex}][unit_price]`
        );


        packageServiceIndex++;


        $('#package-services-wrapper').append(row);


        initializePackageSelect(
            row.find('select')
        );


        calculatePackagePrice();

    });


    // --------------------------------------------------
    // Inventory Item Selected
    // --------------------------------------------------

    $(document).on(
        'change',
        '.inventory-item-select',
        function () {

            let price = parseFloat(
                $(this)
                    .find(':selected')
                    .data('price')
            ) || 0;


            $(this)
                .closest('tr')
                .find('.item-unit-price')
                .val(price);


            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Service Selected
    // --------------------------------------------------

    $(document).on(
        'change',
        '.package-service-select',
        function () {

            let price = parseFloat(
                $(this)
                    .find(':selected')
                    .data('price')
            ) || 0;


            $(this)
                .closest('tr')
                .find('.service-unit-price')
                .val(price);


            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Quantity / Unit Price Changed
    // --------------------------------------------------

    $(document).on(
        'input',
        '#package-items-wrapper input, #package-services-wrapper input',
        function () {

            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Discount Changed
    // --------------------------------------------------

    $(document).on(
        'input',
        '#package-discount',
        function () {

            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Remove Inventory Item
    // --------------------------------------------------

    $(document).on(
        'click',
        '.remove-package-item',
        function () {

            $(this)
                .closest('tr')
                .remove();


            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Remove Service
    // --------------------------------------------------

    $(document).on(
        'click',
        '.remove-package-service',
        function () {

            $(this)
                .closest('tr')
                .remove();


            calculatePackagePrice();

        }
    );


    // --------------------------------------------------
    // Initialize Existing Select2
    // --------------------------------------------------

    $('#package-items-wrapper .inventory-item-select').each(
        function () {

            initializePackageSelect(this);

        }
    );


    $('#package-services-wrapper .package-service-select').each(
        function () {

            initializePackageSelect(this);

        }
    );


    // --------------------------------------------------
    // Initial Calculation
    // --------------------------------------------------

    calculatePackagePrice();

</script>