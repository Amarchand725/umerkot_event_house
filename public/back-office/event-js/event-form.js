$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Select2
    |--------------------------------------------------------------------------
    */

    function initializeSelect2(container = document) {
    $(container).find('select').each(function () {
        const select = $(this);

        if (select.hasClass('select2-hidden-accessible')) {
            return;
        }

        select.select2({
            width: '100%',
            dropdownParent: select.parent()
        });
    });
    }

    function prepareClonedRow(row) {
        // Remove Select2's generated dropdown containers.
        row.find('.select2').remove();

        // Reset Select2 state on cloned select elements.
        row.find('select').each(function () {
            $(this)
                .removeClass('select2-hidden-accessible')
                .removeAttr('data-select2-id')
                .removeAttr('tabindex')
                .removeAttr('aria-hidden')
                .removeData('select2');

            $(this).find('option').removeAttr('data-select2-id');
        });

        return row;
    }

    initializeSelect2();


    /*
    |--------------------------------------------------------------------------
    | Wizard
    |--------------------------------------------------------------------------
    */

    function showStep(step) {
        step = Number(step);

        if (step < 1 || step > 7) {
            return;
        }

        $('.wizard-step').addClass('d-none');
        $(`[data-step-content="${step}"]`).removeClass('d-none');

        $('#eventWizardTabs .nav-link').removeClass('active');
        $(`#eventWizardTabs .nav-link[data-step="${step}"]`)
            .addClass('active');

        calculateTotals();

        if (step === 7) {
            updatePreview();
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    $('.next-step').on('click', function (e) {
        e.preventDefault();
        showStep($(this).data('next'));
    });

    $('.previous-step').on('click', function (e) {
        e.preventDefault();
        showStep($(this).data('previous'));
    });

    $('#eventWizardTabs').on('click', '.nav-link', function (e) {
        e.preventDefault();
        showStep($(this).data('step'));
    });

    /*
    |--------------------------------------------------------------------------
    | Package
    |--------------------------------------------------------------------------
    */

    $('#package_id').on('change', function () {

        const option = $(this).find(':selected');
        const packageId = option.val();

        /*
        |--------------------------------------------------------------------------
        | No Package Selected
        |--------------------------------------------------------------------------
        */

        if (!packageId) {

            // Hide package details
            $('#preview-package-section').addClass('d-none');

            $('#preview-package-name').text('');
            $('#preview-package-discount').text('');
            $('#preview-package-price').text('');
            $('#preview-package-description').text('');
            $('#preview-package-includes').empty();

            // Reset preview
            updatePackagePreview();

            return;
        }

        $('#preview-package-section').removeClass('d-none');


        /*
        |--------------------------------------------------------------------------
        | Package Details
        |--------------------------------------------------------------------------
        */

        $('#package-detail-name')
            .text(option.data('name') || '-');

        $('#package-detail-price')
            .text(
                'Rs. ' +
                Number(option.data('price') || 0).toLocaleString()
            );

        $('#package-detail-description')
            .text(option.data('description') || '-');


        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        let inventory = option.data('inventory');

        if (typeof inventory === 'string') {

            try {
                inventory = JSON.parse(inventory);
            } catch (error) {
                inventory = [];
            }

        }

        inventory = inventory || [];


        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        let services = option.data('services');

        if (typeof services === 'string') {

            try {
                services = JSON.parse(services);
            } catch (error) {
                services = [];
            }

        }

        services = services || [];


        /*
        |--------------------------------------------------------------------------
        | Package Includes
        |--------------------------------------------------------------------------
        */

        let html = '';


        /*
        | Inventory
        */

        inventory.forEach(function (item) {

            html += `
                <tr>
                    <td>
                        <span class="badge bg-label-primary">
                            Inventory
                        </span>
                    </td>

                    <td>
                        ${item.item ?? '-'}
                    </td>

                    <td class="text-center">
                        ${item.quantity ?? 0}
                    </td>
                </tr>
            `;

        });


        /*
        | Services
        */

        services.forEach(function (service) {

            html += `
                <tr>
                    <td>
                        <span class="badge bg-label-success">
                            Service
                        </span>
                    </td>

                    <td>
                        <div class="fw-medium">
                            ${service.name ?? '-'}
                        </div>

                        ${
                            service.description
                                ? `<small class="text-muted">
                                    ${service.description}
                                </small>`
                                : ''
                        }
                    </td>

                    <td class="text-center">
                        -
                    </td>
                </tr>
            `;

        });


        /*
        |--------------------------------------------------------------------------
        | Empty Package
        |--------------------------------------------------------------------------
        */

        if (!html) {

            html = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        No items or services included in this package.
                    </td>
                </tr>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | Show Package Details
        |--------------------------------------------------------------------------
        */

        $('#package-detail-includes').html(html);

        $('#package-details').removeClass('d-none');


        /*
        |--------------------------------------------------------------------------
        | Update Preview
        |--------------------------------------------------------------------------
        */

        updatePackagePreview();

    });

    /*
    |--------------------------------------------------------------------------
    | Load Selected Package On Edit
    |--------------------------------------------------------------------------
    */

    $('#package_id').trigger('change');


    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    const inventoryContainer = $('#inventory-items');
    const addInventoryButton = $('#add-inventory');

    let inventoryIndex =
        inventoryContainer.find('.inventory-row').length;


    /*
    |--------------------------------------------------------------------------
    | Inventory Category Change
    |--------------------------------------------------------------------------
    */

    inventoryContainer.on(
        'change',
        '.inventory-category',
        async function () {
            const row = $(this).closest('.inventory-row');
            const categoryId = $(this).val();
            const itemSelect = row.find('.inventory-item');
            const priceInput = row.find('.unit-price');

            itemSelect.empty().append(
                new Option('Select Inventory Item', '', true, true)
            );

            priceInput.val(0);
            row.find('.item-subtotal').val('0.00');

            if (!categoryId) {
                itemSelect.prop('disabled', true)
                    .trigger('change');

                calculateTotals();
                return;
            }

            itemSelect.prop('disabled', true)
                .append(new Option('Loading items...', ''));

            try {
                const response = await $.ajax({
                    url: inventoryContainer.data('items-url'),
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        inventory_category_id : categoryId
                    }
                });

                itemSelect.empty().append(
                    new Option('Select Inventory Item', '', true, true)
                );

                if (response.status && Array.isArray(response.data)) {
                    response.data.forEach(function (item) {
                        const option = new Option(item.name, item.ulid);

                        $(option).attr(
                            'data-unit-price',
                            Number(item.price_per_unit) || 0
                        );

                        itemSelect.append(option);
                    });
                }

                itemSelect.prop('disabled', false);
                itemSelect.val('').trigger('change');
            } catch (error) {
                itemSelect.empty().append(
                    new Option('Unable to load items', '')
                );

                itemSelect.prop('disabled', true).trigger('change');

                console.error('Inventory loading failed:', error);
            }

            calculateTotals();
        }
    );


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
                parseFloat(
                    selectedOption.data('unit-price')
                ) || 0;

            row.find('.unit-price')
                .val(unitPrice);

            calculateTotals();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Inventory Quantity / Price
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

    /* Add Inventory */

    addInventoryButton.on('click', function () {
        const firstRow = inventoryContainer.find('.inventory-row:first');

        if (!firstRow.length) {
            return;
        }

        const newRow = prepareClonedRow(firstRow.clone());

        // Category: used for filtering, not submitted.
        newRow.find('.inventory-category')
            .attr('name', `items[${inventoryIndex}][inventory_category_id]`)
            .val('');

        newRow.find('.inventory-item')
            .attr('name', `items[${inventoryIndex}][inventory_item_id]`)
            .val('')
            .prop('disabled', true);

        newRow.find('.quantity')
            .attr('name', `items[${inventoryIndex}][quantity]`)
            .val(1);

        newRow.find('.unit-price')
            .attr('name', `items[${inventoryIndex}][unit_price]`)
            .val(0);

        // Display-only subtotal.
        newRow.find('.item-subtotal').val('0.00');

        inventoryContainer.append(newRow);

        initializeSelect2(newRow);

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
    | Services
    |--------------------------------------------------------------------------
    */

    const serviceContainer = $('#event-services');
    const addServiceButton = $('#add-service');

    let serviceIndex =
        serviceContainer.find('.service-row').length;


    /*
    |--------------------------------------------------------------------------
    | Service Change
    |--------------------------------------------------------------------------
    */

    serviceContainer.on(
        'change',
        '.service',
        function () {

            const row =
                $(this).closest('.service-row');

            const selectedOption =
                $(this).find('option:selected');

            const unitPrice =
                parseFloat(
                    selectedOption.data('unit-price')
                ) || 0;

            row.find('.service-unit-price')
                .val(unitPrice);

            calculateTotals();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Service Quantity
    |--------------------------------------------------------------------------
    */

    serviceContainer.on(
        'input',
        '.service-quantity, .service-unit-price',
        function () {

            calculateTotals();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Add Service
    |--------------------------------------------------------------------------
    */

    /* Add Service */

    addServiceButton.on('click', function () {
        const firstRow = serviceContainer.find('.service-row:first');

        if (!firstRow.length) {
            return;
        }

        const newRow = prepareClonedRow(firstRow.clone());

        newRow.find('.service')
            .attr('name', `services[${serviceIndex}][service_id]`)
            .val('');

        newRow.find('.service-quantity')
            .attr('name', `services[${serviceIndex}][quantity]`)
            .val(1);

        newRow.find('.service-unit-price')
            .attr('name', `services[${serviceIndex}][unit_price]`)
            .val('');

        // Display-only subtotal.
        newRow.find('.service-subtotal').val('0.00');

        serviceContainer.append(newRow);

        initializeSelect2(newRow);

        serviceIndex++;

        calculateTotals();
    });


    /*
    |--------------------------------------------------------------------------
    | Remove Service
    |--------------------------------------------------------------------------
    */

    serviceContainer.on(
        'click',
        '.remove-service',
        function () {

            const rows =
                serviceContainer.find('.service-row');

            if (rows.length === 1) {
                return;
            }

            $(this)
                .closest('.service-row')
                .remove();

            calculateTotals();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Calculate Totals
    |--------------------------------------------------------------------------
    */

    function calculateTotals() {

        /*
        |--------------------------------------------------------------------------
        | Package Total
        |--------------------------------------------------------------------------
        */

        const selectedPackage = $('#package_id').find('option:selected');
        const packageId = selectedPackage.val();

        let packagePrice = 0;

        if (packageId) {
            packagePrice = parseFloat(
                selectedPackage.attr('data-price')
            ) || 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Inventory Total
        |--------------------------------------------------------------------------
        */

        let inventoryTotal = 0;
        let totalItems = 0;

        $('.inventory-row').each(function () {

            const row = $(this);

            const quantity =
                parseFloat(row.find('.quantity').val()) || 0;

            const unitPrice =
                parseFloat(row.find('.unit-price').val()) || 0;

            const subtotal = quantity * unitPrice;

            row.find('.item-subtotal').val(
                subtotal.toFixed(2)
            );

            inventoryTotal += subtotal;
            totalItems += quantity;
        });


        /*
        |--------------------------------------------------------------------------
        | Services Total
        |--------------------------------------------------------------------------
        */

        let servicesTotal = 0;

        $('.service-row').each(function () {

            const row = $(this);

            const quantity =
                parseFloat(row.find('.service-quantity').val()) || 0;

            const unitPrice =
                parseFloat(row.find('.service-unit-price').val()) || 0;

            const subtotal = quantity * unitPrice;

            row.find('.service-subtotal').val(
                subtotal.toFixed(2)
            );

            servicesTotal += subtotal;
        });


        /*
        |--------------------------------------------------------------------------
        | Discount / Advance / Security Deposit
        |--------------------------------------------------------------------------
        */

        const discount =
            parseFloat($('#discount').val()) || 0;

        const advance =
            parseFloat($('#advance_amount').val()) || 0;

        const securityDeposit =
            parseFloat($('#security_deposit').val()) || 0;


        /*
        |--------------------------------------------------------------------------
        | Calculations
        |--------------------------------------------------------------------------
        |
        | Package
        |    +
        | Inventory
        |    +
        | Services
        |    =
        | Total
        |
        | Total - Discount = Grand Total
        |
        | Grand Total - Advance = Remaining
        |
        */

        const total =
            packagePrice +
            inventoryTotal +
            servicesTotal;

        const grandTotal =
            Math.max(total - discount, 0);

        const remainingAmount =
            Math.max(grandTotal - advance, 0);


        /*
        |--------------------------------------------------------------------------
        | Inventory Summary
        |--------------------------------------------------------------------------
        */

        $('#inventory-items-total').text(totalItems);

        $('#inventory-total').text(
            inventoryTotal.toFixed(2)
        );


        /*
        |--------------------------------------------------------------------------
        | General / Hidden Totals
        |--------------------------------------------------------------------------
        */

        $('#subtotal').val(
            total.toFixed(2)
        );

        $('#services-total').text(
            servicesTotal.toFixed(2)
        );


        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUMMARY
        |--------------------------------------------------------------------------
        */

        $('#payment-package-total').text(
            packagePrice.toFixed(2)
        );

        $('#payment-inventory-total').text(
            inventoryTotal.toFixed(2)
        );

        $('#payment-services-total').text(
            servicesTotal.toFixed(2)
        );

        $('#payment-discount').text(
            discount.toFixed(2)
        );

        $('#payment-total').text(
            total.toFixed(2)
        );

        $('#grand-total').text(
            grandTotal.toFixed(2)
        );

        $('#payment-advance').text(
            advance.toFixed(2)
        );

        $('#remaining-total').text(
            remainingAmount.toFixed(2)
        );


        /*
        |--------------------------------------------------------------------------
        | PREVIEW SUMMARY
        |--------------------------------------------------------------------------
        */

        $('#preview-package-total').text(
            packagePrice.toFixed(2)
        );

        $('#preview-inventory-total').text(
            inventoryTotal.toFixed(2)
        );

        $('#preview-services-total').text(
            servicesTotal.toFixed(2)
        );

        $('#preview-discount').text(
            discount.toFixed(2)
        );

        $('#preview-grand-total').text(
            grandTotal.toFixed(2)
        );

        $('#preview-security-deposit').text(
            securityDeposit.toFixed(2)
        );

        $('#preview-advance').text(
            advance.toFixed(2)
        );

        $('#preview-remaining').text(
            remainingAmount.toFixed(2)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Discount / Advance / Deposit
    |--------------------------------------------------------------------------
    */

    $('#discount, #advance_amount, #security_deposit')
        .on('input', function () {

            calculateTotals();

        });


    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    function updatePackagePreview() {

        const option = $('#package_id').find(':selected');

        const packageId = option.val();

        if (!packageId) {

            $('#preview-package-name').text('-');

            $('#preview-package-discount').text('0%');

            $('#preview-package-price').text('Rs. 0.00');

            $('#preview-package-description').text('-');

            $('#preview-package-includes').html(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        No package selected.
                    </td>
                </tr>
            `);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Package Details
        |--------------------------------------------------------------------------
        */

        $('#preview-package-name')
            .text(option.data('name') || '-');

        $('#preview-package-discount')
            .text(
                (option.data('discount') ?? 0) + '%'
            );

        $('#preview-package-price')
            .text(
                'Rs. ' +
                Number(option.data('price') || 0).toLocaleString()
            );

        $('#preview-package-description')
            .text(option.data('description') || '-');


        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        let inventory = option.data('inventory');

        if (typeof inventory === 'string') {

            try {
                inventory = JSON.parse(inventory);
            } catch (error) {
                inventory = [];
            }

        }

        inventory = inventory || [];


        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        let services = option.data('services');

        if (typeof services === 'string') {

            try {
                services = JSON.parse(services);
            } catch (error) {
                services = [];
            }

        }

        services = services || [];


        /*
        |--------------------------------------------------------------------------
        | Build Includes Table
        |--------------------------------------------------------------------------
        */

        let html = '';


        // Inventory
        inventory.forEach(function (item) {

            html += `
                <tr>

                    <td>
                        <span class="badge bg-label-primary">
                            Inventory
                        </span>
                    </td>

                    <td>
                        ${item.item ?? '-'}
                    </td>

                    <td class="text-center">
                        ${item.quantity ?? 0}
                    </td>

                </tr>
            `;

        });


        // Services
        services.forEach(function (service) {

            html += `
                <tr>

                    <td>
                        <span class="badge bg-label-success">
                            Service
                        </span>
                    </td>

                    <td>
                        <div class="fw-medium">
                            ${service.name ?? '-'}
                        </div>

                        ${
                            service.description
                                ? `<small class="text-muted">
                                    ${service.description}
                                </small>`
                                : ''
                        }

                    </td>

                    <td class="text-center">
                        -
                    </td>

                </tr>
            `;

        });


        if (!html) {

            html = `
                <tr>

                    <td
                        colspan="3"
                        class="text-center text-muted"
                    >
                        No items or services included.
                    </td>

                </tr>
            `;

        }


        $('#preview-package-includes').html(html);
    }

    /*
    |--------------------------------------------------------------------------
    | Initial Calculation
    |--------------------------------------------------------------------------
    */

    calculateTotals();


    /*
    |--------------------------------------------------------------------------
    | Complete Order Preview
    |--------------------------------------------------------------------------
    */

    function updatePreview() {

        // Helper: safely display user/database values inside HTML.
        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatMoney(value) {
            return (parseFloat(value) || 0).toLocaleString('en-PK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Customer Information
        |--------------------------------------------------------------------------
        */

        const name = $('#customer_name').val() || '';
        const caste = $('#caste').val() || '';

        $('#preview-customer-name').text(
            [name.trim(), caste.trim()].filter(Boolean).join(' ')
        );

        $('#preview-phone').text(
            $('#phone').val() || '-'
        );

        $('#preview-cnic').text(
            $('#cnic_no').val() || '-'
        );

        $('#preview-alternate-phone').text(
            $('#alter_phone').val() || '-'
        );

        $('#preview-address').text(
            $('#address').val() || '-'
        );


        /*
        |--------------------------------------------------------------------------
        | 2. Event Information
        |--------------------------------------------------------------------------
        */

        $('#preview-event-category').text(
            $('#event_category_id option:selected').val()
                ? $('#event_category_id option:selected').text().trim()
                : '-'
        );

        $('#preview-start-date').text(
            $('#start_date').val()?.replace('T', ' ') || '-'
        );

        $('#preview-end-date').text(
            $('#end_date').val()?.replace('T', ' ') || '-'
        );

        $('#preview-venue').text(
            $('#venue').val() || '-'
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Package Information
        |--------------------------------------------------------------------------
        |
        | Reuse your existing package preview function.
        |
        */

        updatePackagePreview();


        /*
        |--------------------------------------------------------------------------
        | 4. Selected Inventory Items
        |--------------------------------------------------------------------------
        */

        let inventoryHtml = '';

        $('#inventory-items .inventory-row').each(function () {
            const row = $(this);
            const itemSelect = row.find('.inventory-item');
            const itemId = itemSelect.val();

            if (!itemId) {
                return;
            }

            const category = row.find('.inventory-category option:selected').text().trim();
            const itemName = itemSelect.find('option:selected').text().trim();
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            const subtotal = quantity * unitPrice;

            inventoryHtml += `
                <tr>
                    <td>${escapeHtml(category || '-')}</td>
                    <td>${escapeHtml(itemName || '-')}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${formatMoney(unitPrice)}</td>
                    <td>Rs. ${formatMoney(subtotal)}</td>
                </tr>
            `;
        });

        $('#preview-inventory-items').html(inventoryHtml);
        $('#preview-inventory-section').toggle(inventoryHtml !== '');

        /*
        |--------------------------------------------------------------------------
        | 5. Selected Services
        |--------------------------------------------------------------------------
        */

        let servicesHtml = '';

        $('#event-services .service-row').each(function () {
            const row = $(this);
            const serviceSelect = row.find('.service');
            const serviceId = serviceSelect.val();

            if (!serviceId) {
                return;
            }

            const serviceName = serviceSelect.find('option:selected').text().trim();
            const quantity = parseFloat(row.find('.service-quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.service-unit-price').val()) || 0;
            const subtotal = quantity * unitPrice;

            servicesHtml += `
                <tr>
                    <td>${escapeHtml(serviceName || '-')}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${formatMoney(unitPrice)}</td>
                    <td>Rs. ${formatMoney(subtotal)}</td>
                </tr>
            `;
        });

        $('#preview-services').html(servicesHtml);
        $('#preview-services-section').toggle(servicesHtml !== '');

        /*
        |--------------------------------------------------------------------------
        | 6. Financial Summary
        |--------------------------------------------------------------------------
        |
        | calculateTotals() already updates the preview totals.
        | Call it here to ensure the latest values are displayed.
        |
        */

        calculateTotals();
    }
});
