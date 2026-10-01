{{-- ============================================================
    EVENT CREATE / EDIT WIZARD
============================================================= --}}

<div class="card">

    {{-- ========================================================
        WIZARD TABS
    ========================================================= --}}
    <div class="card-header border-bottom">

        <ul class="nav nav-tabs card-header-tabs" id="eventWizardTabs">

            {{-- Step 1 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link active"
                    data-step="1"
                >
                    <i class="ti ti-user me-1"></i>
                    1. Customer
                </button>
            </li>

            {{-- Step 2 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="2"
                >
                    <i class="ti ti-calendar-event me-1"></i>
                    2. Event
                </button>
            </li>

            {{-- Step 3 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="3"
                >
                    <i class="ti ti-package me-1"></i>
                    3. Inventory
                </button>
            </li>

            {{-- Step 4 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="4"
                >
                    <i class="ti ti-tool me-1"></i>
                    4. Services
                </button>
            </li>

            {{-- Step 5 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="5"
                >
                    <i class="ti ti-credit-card me-1"></i>
                    5. Payment
                </button>
            </li>

            {{-- Step 6 --}}
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="6"
                >
                    <i class="ti ti-file-invoice me-1"></i>
                    6. Preview
                </button>
            </li>

        </ul>

    </div>


    {{-- ========================================================
        WIZARD BODY
    ========================================================= --}}
    <div class="card-body mt-3">

        @csrf

        @if(isset($model))
            @method('PUT')
        @endif


        {{-- =================================================
            STEP 1: CUSTOMER
        ================================================== --}}
        <div
            class="wizard-step"
            data-step-content="1"
        >

            <div class="mb-4">
                <h4 class="mb-1">Customer Information</h4>

                <p class="text-muted mb-0">
                    Enter the customer's basic information.
                </p>
            </div>


            <div class="row g-3">

                {{-- Full Name --}}
                <div class="col-12 col-md-6">

                    <label
                        for="customer_name"
                        class="form-label fw-semibold"
                    >
                        Full Name <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        class="form-control form-control-lg"
                        placeholder="Enter full name"
                        value="{{ old('customer_name', $model->customer_name ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('customer_name') }}
                    </span>

                </div>


                {{-- Caste --}}
                <div class="col-12 col-md-6">

                    <label
                        for="caste"
                        class="form-label fw-semibold"
                    >
                        Caste
                    </label>

                    <input
                        type="text"
                        id="caste"
                        name="caste"
                        class="form-control form-control-lg"
                        placeholder="Enter caste"
                        value="{{ old('caste', $model->caste ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('caste') }}
                    </span>

                </div>


                {{-- CNIC --}}
                <div class="col-12 col-md-6">

                    <label
                        for="cnic"
                        class="form-label fw-semibold"
                    >
                        CNIC
                    </label>

                    <input
                        type="text"
                        id="cnic"
                        name="cnic"
                        class="form-control form-control-lg"
                        placeholder="XXXXX-XXXXXXX-X"
                        value="{{ old('cnic', $model->cnic ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('cnic') }}
                    </span>

                </div>


                {{-- Phone --}}
                <div class="col-12 col-md-6">

                    <label
                        for="phone"
                        class="form-label fw-semibold"
                    >
                        Phone <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control form-control-lg"
                        placeholder="Enter phone number"
                        value="{{ old('phone', $model->phone ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('phone') }}
                    </span>

                </div>


                {{-- Alternate Phone --}}
                <div class="col-12 col-md-6">

                    <label
                        for="alternate_phone"
                        class="form-label fw-semibold"
                    >
                        Alternate Phone
                    </label>

                    <input
                        type="text"
                        id="alternate_phone"
                        name="alternate_phone"
                        class="form-control form-control-lg"
                        placeholder="Enter alternate phone"
                        value="{{ old('alternate_phone', $model->alternate_phone ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('alternate_phone') }}
                    </span>

                </div>


                {{-- Address --}}
                <div class="col-12">

                    <label
                        for="address"
                        class="form-label fw-semibold"
                    >
                        Full Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        class="form-control"
                        rows="3"
                        placeholder="Enter customer address"
                    >{{ old('address', $model->address ?? '') }}</textarea>

                    <span class="text-danger error">
                        {{ $errors->first('address') }}
                    </span>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-end mt-4">

                <button
                    type="button"
                    class="btn btn-primary next-step"
                    data-next="2"
                >
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- =================================================
            STEP 2: EVENT
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="2"
        >

            <div class="mb-4">

                <h4 class="mb-1">
                    Event Information
                </h4>

                <p class="text-muted mb-0">
                    Enter the event details and venue information.
                </p>

            </div>


            <div class="row g-3">

                {{-- Event Category --}}
                <div class="col-12">

                    <label
                        for="event_category_id"
                        class="form-label fw-semibold"
                    >
                        Event Category <span class="text-danger">*</span>
                    </label>

                    <select
                        id="event_category_id"
                        name="event_category_id"
                        class="form-select form-select-lg"
                    >

                        <option value="">
                            Select Event Category
                        </option>

                        @foreach($eventCategories as $category)

                            <option
                                value="{{ $category->id }}"
                                @selected(
                                    old(
                                        'event_category_id',
                                        $model->event_category_id ?? ''
                                    ) == $category->id
                                )
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                    <span class="text-danger error">
                        {{ $errors->first('event_category_id') }}
                    </span>

                </div>


                {{-- Start Date --}}
                <div class="col-12 col-md-6">

                    <label
                        for="start_date"
                        class="form-label fw-semibold"
                    >
                        Start Date & Time
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="start_date"
                        name="start_date"
                        class="form-control form-control-lg"
                        value="{{ old(
                            'start_date',
                            isset($model->start_date)
                                ? \Carbon\Carbon::parse($model->start_date)->format('Y-m-d\TH:i')
                                : ''
                        ) }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('start_date') }}
                    </span>

                </div>


                {{-- End Date --}}
                <div class="col-12 col-md-6">

                    <label
                        for="end_date"
                        class="form-label fw-semibold"
                    >
                        End Date & Time
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="end_date"
                        name="end_date"
                        class="form-control form-control-lg"
                        value="{{ old(
                            'end_date',
                            isset($model->end_date)
                                ? \Carbon\Carbon::parse($model->end_date)->format('Y-m-d\TH:i')
                                : ''
                        ) }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('end_date') }}
                    </span>

                </div>


                {{-- Venue --}}
                <div class="col-12">

                    <label
                        for="venue"
                        class="form-label fw-semibold"
                    >
                        Venue <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="venue"
                        name="venue"
                        class="form-control form-control-lg"
                        placeholder="Enter event venue"
                        value="{{ old('venue', $model->venue ?? '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('venue') }}
                    </span>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">

                <button
                    type="button"
                    class="btn btn-label-secondary previous-step"
                    data-previous="1"
                >
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button
                    type="button"
                    class="btn btn-primary next-step"
                    data-next="3"
                >
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- =================================================
            STEP 3: INVENTORY
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="3"
        >

            <div class="mb-4">

                <h4 class="mb-1">
                    Event Inventory
                </h4>

                <p class="text-muted mb-0">
                    Select inventory category and required items.
                </p>

            </div>


            <div class="card border shadow-none">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Inventory Items
                    </h5>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="add-inventory"
                    >
                        <i class="ti ti-plus me-1"></i>
                        Add More
                    </button>

                </div>


                <div class="card-body">

                    <div id="inventory-items">

                        @forelse($model->eventItems ?? [] as $index => $eventItem)

                            <div class="inventory-row row g-3 align-items-end mb-3">

                                {{-- Inventory Category --}}
                                <div class="col-12 col-md-3">

                                    <label class="form-label fw-semibold">
                                        Category
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[{{ $index }}][inventory_category_id]"
                                        class="form-select inventory-category"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        @foreach($inventoryCategories as $category)

                                            <option
                                                value="{{ $category->id }}"
                                                @selected(
                                                    $eventItem->inventory_item->inventory_category_id == $category->id
                                                )
                                            >
                                                {{ $category->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Inventory Item --}}
                                <div class="col-12 col-md-3">

                                    <label class="form-label fw-semibold">
                                        Inventory Item
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[{{ $index }}][inventory_item_id]"
                                        class="form-select inventory-item"
                                        data-selected="{{ $eventItem->inventory_item_id }}"
                                    >

                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                        @foreach($inventoryItems as $item)

                                            @if(
                                                $item->inventory_category_id ==
                                                $eventItem->inventory_item->inventory_category_id
                                            )

                                                <option
                                                    value="{{ $item->id }}"
                                                    data-unit-price="{{ $item->price_per_unit }}"
                                                    @selected(
                                                        $eventItem->inventory_item_id == $item->id
                                                    )
                                                >
                                                    {{ $item->name }}
                                                </option>

                                            @endif

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Quantity --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Quantity
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="items[{{ $index }}][quantity]"
                                        class="form-control quantity"
                                        min="1"
                                        value="{{ $eventItem->quantity }}"
                                    />

                                </div>


                                {{-- Unit Price --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        name="items[{{ $index }}][unit_price]"
                                        class="form-control unit-price"
                                        min="0"
                                        step="0.01"
                                        value="{{ $eventItem->price_per_unit }}"
                                        readonly
                                    />

                                </div>


                                {{-- Subtotal --}}
                                <div class="col-12 col-md-1">

                                    <label class="form-label fw-semibold">
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control item-subtotal"
                                        value="{{ number_format($eventItem->subtotal, 2) }}"
                                        readonly
                                    />

                                </div>


                                {{-- Remove --}}
                                <div class="col-12 col-md-1">

                                    <button
                                        type="button"
                                        class="btn btn-danger remove-inventory w-100"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>

                                </div>

                            </div>

                        @empty

                            {{-- Default Inventory Row --}}
                            <div class="inventory-row row g-3 align-items-end mb-3">

                                {{-- Category --}}
                                <div class="col-12 col-md-3">

                                    <label class="form-label fw-semibold">
                                        Category
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[0][inventory_category_id]"
                                        class="form-select inventory-category"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        @foreach($inventoryCategories as $category)

                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Item --}}
                                <div class="col-12 col-md-3">

                                    <label class="form-label fw-semibold">
                                        Inventory Item
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[0][inventory_item_id]"
                                        class="form-select inventory-item"
                                        disabled
                                    >

                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                    </select>

                                </div>


                                {{-- Quantity --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Quantity
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="items[0][quantity]"
                                        class="form-control quantity"
                                        min="1"
                                        value="1"
                                    />

                                </div>


                                {{-- Unit Price --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        name="items[0][unit_price]"
                                        class="form-control unit-price"
                                        min="0"
                                        step="0.01"
                                        readonly
                                    />

                                </div>


                                {{-- Subtotal --}}
                                <div class="col-12 col-md-1">

                                    <label class="form-label fw-semibold">
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control item-subtotal"
                                        value="0.00"
                                        readonly
                                    />

                                </div>


                                {{-- Remove --}}
                                <div class="col-12 col-md-1">

                                    <button
                                        type="button"
                                        class="btn btn-danger remove-inventory w-100"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>

                                </div>

                            </div>

                        @endforelse

                    </div>


                    {{-- Inventory Summary --}}
                    <div class="row justify-content-end mt-4">

                        <div class="col-12 col-md-5">

                            <div class="d-flex justify-content-between align-items-center border-top pt-3">

                                <h5 class="mb-0">
                                    Total Items
                                </h5>

                                <h4 class="mb-0">
                                    <span id="inventory-items-total">
                                        0
                                    </span>
                                </h4>

                            </div>


                            <div class="d-flex justify-content-between align-items-center border-top pt-3">

                                <h5 class="mb-0">
                                    Inventory Total
                                </h5>

                                <h4 class="mb-0">
                                    Rs.
                                    <span id="inventory-total">
                                        0.00
                                    </span>
                                </h4>

                            </div>


                            <input
                                type="hidden"
                                name="subtotal"
                                id="subtotal"
                                value="{{ old('subtotal', $model->subtotal ?? 0) }}"
                            />

                        </div>

                    </div>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">

                <button
                    type="button"
                    class="btn btn-label-secondary previous-step"
                    data-previous="2"
                >
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button
                    type="button"
                    class="btn btn-primary next-step"
                    data-next="4"
                >
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- =================================================
            STEP 4: SERVICES
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="4"
        >

            <div class="mb-4">

                <h4 class="mb-1">
                    Event Services
                </h4>

                <p class="text-muted mb-0">
                    Select additional services required for this event.
                </p>

            </div>


            <div class="card border shadow-none">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Services
                    </h5>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="add-service"
                    >
                        <i class="ti ti-plus me-1"></i>
                        Add More
                    </button>

                </div>


                <div class="card-body">

                    <div id="event-services">

                        @forelse($model->eventServices ?? [] as $index => $eventService)

                            <div class="service-row row g-3 align-items-end mb-3">

                                {{-- Service --}}
                                <div class="col-12 col-md-5">

                                    <label class="form-label fw-semibold">
                                        Service
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="services[{{ $index }}][service_id]"
                                        class="form-select service"
                                    >

                                        <option value="">
                                            Select Service
                                        </option>

                                        @foreach($services as $service)

                                            <option
                                                value="{{ $service->id }}"
                                                data-unit-price="{{ $service->price }}"
                                                @selected(
                                                    $eventService->service_id == $service->id
                                                )
                                            >
                                                {{ $service->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Quantity --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Quantity
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="services[{{ $index }}][quantity]"
                                        class="form-control service-quantity"
                                        min="1"
                                        value="{{ $eventService->quantity }}"
                                    />

                                </div>


                                {{-- Unit Price --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        name="services[{{ $index }}][unit_price]"
                                        class="form-control service-unit-price"
                                        min="0"
                                        step="0.01"
                                        value="{{ $eventService->unit_price }}"
                                        readonly
                                    />

                                </div>


                                {{-- Subtotal --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control service-subtotal"
                                        value="{{ number_format($eventService->subtotal, 2) }}"
                                        readonly
                                    />

                                </div>


                                {{-- Remove --}}
                                <div class="col-12 col-md-1">

                                    <button
                                        type="button"
                                        class="btn btn-danger remove-service w-100"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>

                                </div>

                            </div>

                        @empty

                            {{-- Default Service Row --}}
                            <div class="service-row row g-3 align-items-end mb-3">

                                {{-- Service --}}
                                <div class="col-12 col-md-5">

                                    <label class="form-label fw-semibold">
                                        Service
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="services[0][service_id]"
                                        class="form-select service"
                                    >

                                        <option value="">
                                            Select Service
                                        </option>

                                        @foreach($services as $service)

                                            <option
                                                value="{{ $service->id }}"
                                                data-unit-price="{{ $service->price }}"
                                            >
                                                {{ $service->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Quantity --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Quantity
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="services[0][quantity]"
                                        class="form-control service-quantity"
                                        min="1"
                                        value="1"
                                    />

                                </div>


                                {{-- Unit Price --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Unit Price
                                    </label>

                                    <input
                                        type="number"
                                        name="services[0][unit_price]"
                                        class="form-control service-unit-price"
                                        min="0"
                                        step="0.01"
                                        readonly
                                    />

                                </div>


                                {{-- Subtotal --}}
                                <div class="col-12 col-md-2">

                                    <label class="form-label fw-semibold">
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control service-subtotal"
                                        value="0.00"
                                        readonly
                                    />

                                </div>


                                {{-- Remove --}}
                                <div class="col-12 col-md-1">

                                    <button
                                        type="button"
                                        class="btn btn-danger remove-service w-100"
                                    >
                                        <i class="ti ti-trash"></i>
                                    </button>

                                </div>

                            </div>

                        @endforelse

                    </div>


                    {{-- Services Summary --}}
                    <div class="row justify-content-end mt-4">

                        <div class="col-12 col-md-5">

                            <div class="d-flex justify-content-between align-items-center border-top pt-3">

                                <h5 class="mb-0">
                                    Services Total
                                </h5>

                                <h4 class="mb-0">
                                    Rs.
                                    <span id="services-total">
                                        0.00
                                    </span>
                                </h4>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">

                <button
                    type="button"
                    class="btn btn-label-secondary previous-step"
                    data-previous="3"
                >
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button
                    type="button"
                    class="btn btn-primary next-step"
                    data-next="5"
                >
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- =================================================
            STEP 5: PAYMENT
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="5"
        >

            <div class="mb-4">

                <h4 class="mb-1">
                    Payment
                </h4>

                <p class="text-muted mb-0">
                    Enter payment details and review the event total.
                </p>

            </div>


            <div class="row g-3">

                {{-- Payment Method --}}
                <div class="col-12">

                    <label
                        for="payment_method_id"
                        class="form-label fw-semibold"
                    >
                        Payment Method
                    </label>

                    <select
                        id="payment_method_id"
                        name="payment_method_id"
                        class="form-select"
                    >

                        <option value="">
                            Select Payment Method
                        </option>

                        @foreach($paymentMethods as $paymentMethod)

                            <option
                                value="{{ $paymentMethod->id }}"
                                @selected(
                                    old(
                                        'payment_method_id',
                                        $model->payment_method_id ?? ''
                                    ) == $paymentMethod->id
                                )
                            >
                                {{ $paymentMethod->name }}
                            </option>

                        @endforeach

                    </select>

                    <span
                        id="payment_method_id_error"
                        class="text-danger error"
                    >
                        {{ $errors->first('payment_method_id') }}
                    </span>

                </div>


                {{-- Discount --}}
                <div class="col-12 col-md-4">

                    <label
                        for="discount"
                        class="form-label fw-semibold"
                    >
                        Discount
                    </label>

                    <input
                        type="number"
                        id="discount"
                        name="discount"
                        class="form-control form-control-lg"
                        min="0"
                        step="0.01"
                        value="{{ old('discount', $model->discount ?? 0) }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('discount') }}
                    </span>

                </div>


                {{-- Security Deposit --}}
                <div class="col-12 col-md-4">

                    <label
                        for="security_deposit"
                        class="form-label fw-semibold"
                    >
                        Security Deposit
                    </label>

                    <input
                        type="number"
                        id="security_deposit"
                        name="security_deposit"
                        class="form-control form-control-lg"
                        min="0"
                        step="0.01"
                        value="{{ old('security_deposit', $model->security_deposit ?? 0) }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('security_deposit') }}
                    </span>

                </div>


                {{-- Advance --}}
                <div class="col-12 col-md-4">

                    <label
                        for="advance_amount"
                        class="form-label fw-semibold"
                    >
                        Advance Amount
                    </label>

                    <input
                        type="number"
                        id="advance_amount"
                        name="advance_amount"
                        class="form-control form-control-lg"
                        min="0"
                        step="0.01"
                        value="{{ old('advance_amount', $model->advance_amount ?? 0) }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('advance_amount') }}
                    </span>

                </div>


                {{-- Note --}}
                <div class="col-12">

                    <label
                        for="note"
                        class="form-label fw-semibold"
                    >
                        Note
                    </label>

                    <textarea
                        id="note"
                        name="note"
                        class="form-control"
                        rows="3"
                        placeholder="Enter any additional notes"
                    >{{ old('note', $model->note ?? '') }}</textarea>

                    <span class="text-danger error">
                        {{ $errors->first('note') }}
                    </span>

                </div>

            </div>


            {{-- Payment Summary --}}
            <div class="row justify-content-end mt-4">

                <div class="col-12 col-md-5">

                    <div class="border rounded p-3">

                        {{-- Inventory Total --}}
                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Inventory Total
                            </span>

                            <strong>
                                Rs.
                                <span id="payment-inventory-total">
                                    0.00
                                </span>
                            </strong>

                        </div>


                        {{-- Services Total --}}
                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Services Total
                            </span>

                            <strong>
                                Rs.
                                <span id="payment-services-total">
                                    0.00
                                </span>
                            </strong>

                        </div>


                        {{-- Discount --}}
                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Discount
                            </span>

                            <strong>
                                Rs.
                                <span id="payment-discount">
                                    0.00
                                </span>
                            </strong>

                        </div>


                        {{-- Grand Total --}}
                        <div class="d-flex justify-content-between border-top pt-2 mb-2">

                            <strong>
                                Grand Total
                            </strong>

                            <strong>
                                Rs.
                                <span id="grand-total">
                                    0.00
                                </span>
                            </strong>

                        </div>


                        {{-- Advance --}}
                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Advance Paid
                            </span>

                            <strong class="text-success">
                                Rs.
                                <span id="payment-advance">
                                    0.00
                                </span>
                            </strong>

                        </div>


                        {{-- Remaining --}}
                        <div class="d-flex justify-content-between border-top pt-2">

                            <strong>
                                Remaining Amount
                            </strong>

                            <strong class="text-primary">
                                Rs.
                                <span id="remaining-total">
                                    0.00
                                </span>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">

                <button
                    type="button"
                    class="btn btn-label-secondary previous-step"
                    data-previous="4"
                >
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button
                    type="button"
                    class="btn btn-primary next-step"
                    data-next="6"
                >
                    Review Order
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- =================================================
            STEP 6: PREVIEW
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="6"
        >

            <div class="mb-4">

                <h4 class="mb-1">
                    Order Preview
                </h4>

                <p class="text-muted mb-0">
                    Review all event details before submitting.
                </p>

            </div>


            {{-- Customer Preview --}}
            <div class="card border shadow-none mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-user me-1"></i>
                        Customer Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <small class="text-muted">
                                Full Name
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-customer-name"
                            >
                                -
                            </div>
                        </div>


                        <div class="col-md-6">
                            <small class="text-muted">
                                Phone
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-phone"
                            >
                                -
                            </div>
                        </div>


                        <div class="col-md-6">
                            <small class="text-muted">
                                CNIC
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-cnic"
                            >
                                -
                            </div>
                        </div>


                        <div class="col-md-6">
                            <small class="text-muted">
                                Alternate Phone
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-alternate-phone"
                            >
                                -
                            </div>
                        </div>


                        <div class="col-12">
                            <small class="text-muted">
                                Address
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-address"
                            >
                                -
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Event Preview --}}
            <div class="card border shadow-none mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="ti ti-calendar-event me-1"></i>
                        Event Information
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-4">

                            <small class="text-muted">
                                Event Category
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-event-category"
                            >
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Start Date
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-start-date"
                            >
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                End Date
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-end-date"
                            >
                                -
                            </div>

                        </div>


                        <div class="col-12">

                            <small class="text-muted">
                                Venue
                            </small>

                            <div
                                class="fw-semibold"
                                id="preview-venue"
                            >
                                -
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Inventory Preview --}}
            <div class="card border shadow-none mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="ti ti-package me-1"></i>
                        Inventory
                    </h5>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>
                                        Category
                                    </th>

                                    <th>
                                        Item
                                    </th>

                                    <th>
                                        Qty
                                    </th>

                                    <th>
                                        Unit Price
                                    </th>

                                    <th>
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="preview-inventory-items">

                                <tr>
                                    <td
                                        colspan="5"
                                        class="text-center text-muted"
                                    >
                                        No inventory items selected.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- Services Preview --}}
            <div class="card border shadow-none mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="ti ti-tool me-1"></i>
                        Services
                    </h5>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>
                                        Service
                                    </th>

                                    <th>
                                        Qty
                                    </th>

                                    <th>
                                        Unit Price
                                    </th>

                                    <th>
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="preview-services">

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center text-muted"
                                    >
                                        No services selected.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- Financial Preview --}}
            <div class="row justify-content-end">

                <div class="col-12 col-md-5">

                    <div class="card border shadow-none">

                        <div class="card-header">

                            <h5 class="mb-0">
                                Order Summary
                            </h5>

                        </div>


                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Inventory Total
                                </span>

                                <strong>
                                    Rs.
                                    <span id="preview-inventory-total">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Services Total
                                </span>

                                <strong>
                                    Rs.
                                    <span id="preview-services-total">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Discount
                                </span>

                                <strong>
                                    Rs.
                                    <span id="preview-discount">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between border-top pt-2 mb-2">

                                <strong>
                                    Grand Total
                                </strong>

                                <strong>
                                    Rs.
                                    <span id="preview-grand-total">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Security Deposit
                                </span>

                                <strong>
                                    Rs.
                                    <span id="preview-security-deposit">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between mb-2">

                                <span>
                                    Advance Paid
                                </span>

                                <strong class="text-success">
                                    Rs.
                                    <span id="preview-advance">
                                        0.00
                                    </span>
                                </strong>

                            </div>


                            <div class="d-flex justify-content-between border-top pt-2">

                                <strong>
                                    Remaining Amount
                                </strong>

                                <strong class="text-primary">
                                    Rs.
                                    <span id="preview-remaining">
                                        0.00
                                    </span>
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">

                <button
                    type="button"
                    class="btn btn-label-secondary previous-step"
                    data-previous="5"
                >
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>


                <button
                    type="submit"
                    class="btn btn-success"
                >

                    <i class="ti ti-check me-1"></i>

                    {{ isset($model) ? 'Update Event' : 'Create Event' }}

                </button>

            </div>

        </div>

    </div>

</div>


<script src="{{ asset('back-office/event-js/event-form.js') }}"></script>
