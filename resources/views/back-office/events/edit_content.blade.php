@method('PUT')

<div class="row g-4">

    {{-- ============================================================
        1. CUSTOMER INFORMATION
    ============================================================= --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">1. Customer Information</h5>
            </div>

            <div class="card-body">
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
                            value="{{ old('customer_name', $model->customer?->name) }}"
                        />

                        <span id="customer_name_error" class="text-danger error">
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
                            value="{{ old('caste', $model->customer?->caste) }}"
                        />

                        <span id="caste_error" class="text-danger error">
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
                            value="{{ old('cnic', $model->customer?->cnic) }}"
                        />

                        <span id="cnic_error" class="text-danger error">
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
                            value="{{ old('phone', $model->customer?->phone) }}"
                        />

                        <span id="phone_error" class="text-danger error">
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
                            value="{{ old('alternate_phone', $model->customer?->alternate_phone) }}"
                        />

                        <span id="alternate_phone_error" class="text-danger error">
                            {{ $errors->first('alternate_phone') }}
                        </span>
                    </div>

                    {{-- Address --}}
                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            class="form-control"
                            rows="3"
                            placeholder="Enter customer address"
                        >{{ old('address', $model->customer?->address) }}</textarea>

                        <span id="address_error" class="text-danger error">
                            {{ $errors->first('address') }}
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================
        2. EVENT INFORMATION
    ============================================================= --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">2. Event Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    {{-- Event Category --}}
                    <div class="col-12 col-md-4">
                        <label for="event_category_id" class="form-label fw-semibold">
                            Event Category <span class="text-danger">*</span>
                        </label>

                        <select
                            id="event_category_id"
                            name="event_category_id"
                            class="form-select"
                        >
                            <option value="">Select Event Category</option>

                            @foreach ($eventCategories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        old('event_category_id', $model->event_category_id)
                                        == $category->id
                                    )
                                >
                                    {{ ucfirst($category->name) }}
                                </option>
                            @endforeach
                        </select>

                        <span id="event_category_id_error" class="text-danger error">
                            {{ $errors->first('event_category_id') }}
                        </span>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-12 col-md-4">
                        <label for="start_date" class="form-label fw-semibold">
                            Start Date & Time <span class="text-danger">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="start_date"
                            name="start_date"
                            class="form-control form-control-lg"
                            value="{{ old('start_date', $model->start_date ? \Carbon\Carbon::parse($model->start_date)->format('Y-m-d\TH:i') : '') }}"
                        />

                        <span id="start_date_error" class="text-danger error">
                            {{ $errors->first('start_date') }}
                        </span>
                    </div>

                    {{-- End Date --}}
                    <div class="col-12 col-md-4">
                        <label for="end_date" class="form-label fw-semibold">
                            End Date & Time <span class="text-danger">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="end_date"
                            name="end_date"
                            class="form-control form-control-lg"
                            value="{{ old('end_date', $model->end_date ? \Carbon\Carbon::parse($model->end_date)->format('Y-m-d\TH:i') : '') }}"
                        />

                        <span id="end_date_error" class="text-danger error">
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
                            value="{{ old('venue', $model->venue) }}"
                        />

                        <span id="venue_error" class="text-danger error">
                            {{ $errors->first('venue') }}
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================
        3. EVENT INVENTORY
    ============================================================= --}}
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">3. Event Inventory</h5>

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

                    @forelse ($model->eventItems as $index => $eventItem)

                        <div class="inventory-row row g-3 align-items-end mb-3">

                            {{-- Inventory Item --}}
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold">
                                    Inventory Item <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="items[{{ $index }}][inventory_item_id]"
                                    class="form-select inventory-item"
                                >
                                    <option value="">
                                        Select Inventory Item
                                    </option>

                                    @foreach ($inventoryItems as $item)
                                        <option
                                            value="{{ $item->id }}"
                                            data-unit-price="{{ $item->unit_price }}"
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
                                    Quantity <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][quantity]"
                                    class="form-control quantity"
                                    min="1"
                                    value="{{ old("items.$index.quantity", $eventItem->quantity) }}"
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
                                    value="{{ old("items.$index.unit_price", $eventItem->unit_price) }}"
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
                                    value="{{ number_format($eventItem->subtotal, 2, '.', '') }}"
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

                        {{-- Empty fallback row --}}
                        <div class="inventory-row row g-3 align-items-end mb-3">

                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold">
                                    Inventory Item <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="items[0][inventory_item_id]"
                                    class="form-select inventory-item"
                                >
                                    <option value="">
                                        Select Inventory Item
                                    </option>

                                    @foreach ($inventoryItems as $item)
                                        <option
                                            value="{{ $item->id }}"
                                            data-unit-price="{{ $item->unit_price }}"
                                        >
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">
                                    Quantity
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
                                    value="0"
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


                {{-- Inventory Total --}}
                <div class="row justify-content-end mt-4">
                    <div class="col-12 col-md-4">

                        <div class="d-flex justify-content-between border-top pt-3">

                            <h5 class="mb-0">
                                Total
                            </h5>

                            <h4 class="mb-0">
                                Rs. <span id="inventory-total">0.00</span>
                            </h4>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ============================================================
        4. PAYMENT
    ============================================================= --}}
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">4. Payment</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Discount --}}
                    <div class="col-12 col-md-4">
                        <label for="discount" class="form-label fw-semibold">
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

                        <span id="discount_error" class="text-danger error">
                            {{ $errors->first('discount') }}
                        </span>
                    </div>

                    {{-- Security Deposit --}}
                    <div class="col-12 col-md-4">
                        <label for="security_deposit" class="form-label fw-semibold">
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

                        <span id="security_deposit_error" class="text-danger error">
                            {{ $errors->first('security_deposit') }}
                        </span>
                    </div>

                    {{-- Advance Amount --}}
                    <div class="col-12 col-md-4">
                        <label for="advance_amount" class="form-label fw-semibold">
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

                        <span id="advance_amount_error" class="text-danger error">
                            {{ $errors->first('advance_amount') }}
                        </span>
                    </div>

                    {{-- Note --}}
                    <div class="col-12">
                        <label for="note" class="form-label fw-semibold">
                            Note
                        </label>

                        <textarea
                            id="note"
                            name="note"
                            class="form-control"
                            rows="3"
                            placeholder="Enter any additional notes"
                        >{{ old('note', $model->note) }}</textarea>

                        <span id="note_error" class="text-danger error">
                            {{ $errors->first('note') }}
                        </span>
                    </div>

                </div>


                {{-- Payment Summary --}}
                <div class="row justify-content-end mt-4">
                    <div class="col-12 col-md-5">

                        <div class="border rounded p-3">

                            <div class="d-flex justify-content-between mb-2">
                                <span>Inventory Total</span>

                                <strong>
                                    Rs. <span id="payment-total">0.00</span>
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount</span>

                                <strong>
                                    Rs. <span id="payment-discount">0.00</span>
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between border-top pt-2">
                                <strong>Grand Total</strong>

                                <strong>
                                    Rs. <span id="grand-total">0.00</span>
                                </strong>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ============================================================
        ACTIONS
    ============================================================= --}}
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">

            <a
                href="{{ route('events.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="ti ti-device-floppy me-1"></i>
                Update Event
            </button>

        </div>
    </div>

</div>


<script>
    $(document).ready(function () {

        /*
        |--------------------------------------------------------------------------
        | Select2
        |--------------------------------------------------------------------------
        */

        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });


        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        const inventoryContainer = $('#inventory-items');
        const addInventoryButton = $('#add-inventory');

        let inventoryIndex = {{ $model->eventItems->count() }};


        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */

        function calculateTotals() {

            let total = 0;

            $('.inventory-row').each(function () {

                const row = $(this);

                const quantity =
                    parseFloat(row.find('.quantity').val()) || 0;

                const unitPrice =
                    parseFloat(row.find('.unit-price').val()) || 0;

                const subtotal =
                    quantity * unitPrice;

                row.find('.item-subtotal')
                    .val(subtotal.toFixed(2));

                total += subtotal;
            });


            const discount =
                parseFloat($('#discount').val()) || 0;

            const grandTotal =
                Math.max(total - discount, 0);


            $('#inventory-total')
                .text(total.toFixed(2));

            $('#payment-total')
                .text(total.toFixed(2));

            $('#payment-discount')
                .text(discount.toFixed(2));

            $('#grand-total')
                .text(grandTotal.toFixed(2));
        }


        /*
        |--------------------------------------------------------------------------
        | Inventory Item Change
        |--------------------------------------------------------------------------
        */

        inventoryContainer.on(
            'change',
            '.inventory-item',
            function () {

                const row = $(this).closest('.inventory-row');

                const selectedOption =
                    $(this).find('option:selected');

                const unitPrice =
                    selectedOption.data('unit-price') || 0;

                row.find('.unit-price')
                    .val(unitPrice);

                calculateTotals();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Quantity / Unit Price Change
        |--------------------------------------------------------------------------
        */

        inventoryContainer.on(
            'input',
            '.quantity, .unit-price',
            function () {

                calculateTotals();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Add Inventory
        |--------------------------------------------------------------------------
        */

        addInventoryButton.on('click', function () {

            const firstRow =
                inventoryContainer.find('.inventory-row:first');

            const newRow =
                firstRow.clone();

            newRow.find('.inventory-item')
                .attr(
                    'name',
                    `items[${inventoryIndex}][inventory_item_id]`
                )
                .val('');

            newRow.find('.quantity')
                .attr(
                    'name',
                    `items[${inventoryIndex}][quantity]`
                )
                .val(1);

            newRow.find('.unit-price')
                .attr(
                    'name',
                    `items[${inventoryIndex}][unit_price]`
                )
                .val(0);

            newRow.find('.item-subtotal')
                .val('0.00');


            // Destroy cloned Select2 markup
            newRow.find('.select2').remove();

            const select =
                newRow.find('.inventory-item');

            select.show();


            inventoryContainer.append(newRow);


            // Initialize Select2 on new row
            select.select2({
                dropdownParent: select.parent()
            });


            inventoryIndex++;

            calculateTotals();
        });


        /*
        |--------------------------------------------------------------------------
        | Remove Inventory
        |--------------------------------------------------------------------------
        */

        inventoryContainer.on(
            'click',
            '.remove-inventory',
            function () {

                const rows =
                    inventoryContainer.find('.inventory-row');

                if (rows.length === 1) {
                    return;
                }

                $(this)
                    .closest('.inventory-row')
                    .remove();

                calculateTotals();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Discount
        |--------------------------------------------------------------------------
        */

        $('#discount').on('input', function () {

            calculateTotals();
        });


        /*
        |--------------------------------------------------------------------------
        | Initial Calculation
        |--------------------------------------------------------------------------
        */

        calculateTotals();

    });

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
