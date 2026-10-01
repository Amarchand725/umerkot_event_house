{{-- ============================================================
    EVENT CREATE / EDIT WIZARD
============================================================= --}}

<div class="card">

    {{-- ========================================================
        WIZARD TABS
    ========================================================= --}}
    <div class="card-header border-bottom">
        <ul class="nav nav-tabs card-header-tabs" id="eventWizardTabs">

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

            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    data-step="4"
                >
                    <i class="ti ti-credit-card me-1"></i>
                    4. Payment
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
                    <label for="customer_name" class="form-label fw-semibold">
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
                    <label for="caste" class="form-label fw-semibold">
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
                    <label for="cnic" class="form-label fw-semibold">
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
                    <label for="phone" class="form-label fw-semibold">
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
                    <label for="alternate_phone" class="form-label fw-semibold">
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
                    <label for="address" class="form-label fw-semibold">
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
                <h4 class="mb-1">Event Information</h4>
                <p class="text-muted mb-0">
                    Enter the event details and venue information.
                </p>
            </div>

            <div class="row g-3">

                {{-- Event Category --}}
                <div class="col-12 col-md-12">
                    <label for="event_category_id" class="form-label fw-semibold">
                        Event Category <span class="text-danger">*</span>
                    </label>

                    <select
                        id="event_category_id"
                        name="event_category_id"
                        class="form-select form-select-lg"
                    >
                        <option value="">Select Event Category</option>

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
                    <label for="start_date" class="form-label fw-semibold">
                        Start Date & Time <span class="text-danger">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="start_date"
                        name="start_date"
                        class="form-control form-control-lg"
                        value="{{ old('start_date', isset($model->start_date) ? \Carbon\Carbon::parse($model->start_date)->format('Y-m-d\TH:i') : '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('start_date') }}
                    </span>
                </div>


                {{-- End Date --}}
                <div class="col-12 col-md-6">
                    <label for="end_date" class="form-label fw-semibold">
                        End Date & Time <span class="text-danger">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="end_date"
                        name="end_date"
                        class="form-control form-control-lg"
                        value="{{ old('end_date', isset($model->end_date) ? \Carbon\Carbon::parse($model->end_date)->format('Y-m-d\TH:i') : '') }}"
                    />

                    <span class="text-danger error">
                        {{ $errors->first('end_date') }}
                    </span>
                </div>


                {{-- Venue --}}
                <div class="col-12">
                    <label for="venue" class="form-label fw-semibold">
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
                <h4 class="mb-1">Event Inventory</h4>
                <p class="text-muted mb-0">
                    Select the inventory items required for this event.
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

                                {{-- Inventory Item --}}
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-semibold">
                                        Inventory Item
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[{{ $index }}][inventory_item_id]"
                                        class="form-select inventory-item"
                                    >
                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                        @foreach($inventoryItems as $item)
                                            <option
                                                value="{{ $item->id }}"
                                                data-unit-price="{{ $item->price_per_unit }}"
                                                @selected($eventItem->inventory_item_id == $item->id)
                                            >
                                                {{ $item->name }}
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
                                <div class="col-12 col-md-2">
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

                                <div class="col-12 col-md-5">

                                    <label class="form-label fw-semibold">
                                        Inventory Item
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="items[0][inventory_item_id]"
                                        class="form-select inventory-item"
                                    >
                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                        @foreach($inventoryItems as $item)
                                            <option
                                                value="{{ $item->id }}"
                                                data-unit-price="{{ $item->price_per_unit }}"
                                            >
                                                {{ $item->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>


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


                                <div class="col-12 col-md-2">

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

                            {{-- Total Items --}}
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">

                                <h5 class="mb-0">
                                    Total Items
                                </h5>

                                <h4 class="mb-0">
                                    <span id="inventory-items-total">0</span>
                                </h4>

                            </div>

                            {{-- Inventory Total --}}
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">

                                <h5 class="mb-0">
                                    Total
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
            STEP 4: PAYMENT
        ================================================== --}}
        <div
            class="wizard-step d-none"
            data-step-content="4"
        >

            <div class="mb-4">
                <h4 class="mb-1">Payment</h4>
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
                                <span id="payment-total">
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
                    data-previous="3"
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