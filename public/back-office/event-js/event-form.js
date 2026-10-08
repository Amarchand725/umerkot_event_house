$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Select2
    |--------------------------------------------------------------------------
    */

    function initializeSelect2(container = document) {

        $(container).find('select').each(function () {

            const select = $(this);

            // Prevent duplicate initialization
            if (select.hasClass('select2-hidden-accessible')) {
                return;
            }

            select.select2({
                dropdownParent: select.parent()
            });

        });

    }

    initializeSelect2();


    /*
    |--------------------------------------------------------------------------
    | Wizard
    |--------------------------------------------------------------------------
    */

    function showStep(step) {

        $('.wizard-step').addClass('d-none');

        $(`[data-step-content="${step}"]`).removeClass('d-none');

        $('#eventWizardTabs .nav-link').removeClass('active');

        $(`#eventWizardTabs .nav-link[data-step="${step}"]`)
            .addClass('active');

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });


        /*
        |--------------------------------------------------------------------------
        | Refresh calculations whenever wizard step changes
        |--------------------------------------------------------------------------
        */

        calculateTotals();


        /*
        |--------------------------------------------------------------------------
        | Preview Step
        |--------------------------------------------------------------------------
        */

        if (step == 6) {
            updatePreview();
        }


        /*
        |--------------------------------------------------------------------------
        | Payment Step
        |--------------------------------------------------------------------------
        |
        | Refresh the payment summary when Payment tab is opened.
        |
        */

        if (step == 5) {
            calculateTotals();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Next Step
    |--------------------------------------------------------------------------
    */

    $('.next-step').on('click', function () {

        const nextStep = $(this).data('next');

        showStep(nextStep);

    });


    /*
    |--------------------------------------------------------------------------
    | Previous Step
    |--------------------------------------------------------------------------
    */

    $('.previous-step').on('click', function () {

        const previousStep = $(this).data('previous');

        showStep(previousStep);

    });


    /*
    |--------------------------------------------------------------------------
    | Wizard Tab Click
    |--------------------------------------------------------------------------
    */

    $('#eventWizardTabs').on(
        'click',
        '.nav-link',
        function () {

            const step = $(this).data('step');

            showStep(step);

        }
    );

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
            $('#package-details').addClass('d-none');

            // Reset preview
            updatePackagePreview();

            return;
        }


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
        function () {

            const row = $(this).closest('.inventory-row');

            const categoryId = $(this).val();

            const itemSelect = row.find('.inventory-item');

            itemSelect.empty();

            itemSelect.append(
                '<option value="">Select Inventory Item</option>'
            );

            if (!categoryId) {

                itemSelect.prop('disabled', true);

                itemSelect.trigger('change');

                row.find('.unit-price').val(0);

                calculateTotals();

                return;
            }

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

    addInventoryButton.on('click', function () {

        const firstRow =
            inventoryContainer.find('.inventory-row:first');

        const newRow =
            firstRow.clone();

        /*
        | Remove Select2 generated markup
        */

        newRow.find('.select2').remove();

        const category =
            newRow.find('.inventory-category');

        const select =
            newRow.find('.inventory-item');


        /*
        | Reset category
        */

        category
            .attr(
                'name',
                `items[${inventoryIndex}][inventory_category_id]`
            )
            .val('');


        /*
        | Reset inventory item
        */

        select
            .attr(
                'name',
                `items[${inventoryIndex}][inventory_item_id]`
            )
            .val('')
            .prop('disabled', true);


        /*
        | Reset quantity
        */

        newRow.find('.quantity')
            .attr(
                'name',
                `items[${inventoryIndex}][quantity]`
            )
            .val(1);


        /*
        | Reset price
        */

        newRow.find('.unit-price')
            .attr(
                'name',
                `items[${inventoryIndex}][unit_price]`
            )
            .val(0);


        /*
        | Reset subtotal
        */

        newRow.find('.item-subtotal')
            .val('0.00');


        inventoryContainer.append(newRow);


        /*
        | Initialize Select2
        */

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

    addServiceButton.on('click', function () {

        const firstRow =
            serviceContainer.find('.service-row:first');

        const newRow =
            firstRow.clone();

        /*
        | Remove Select2 generated markup
        */

        newRow.find('.select2').remove();

        const select =
            newRow.find('.service');


        select
            .attr(
                'name',
                `services[${serviceIndex}][service_id]`
            )
            .val('');


        newRow.find('.service-quantity')
            .attr(
                'name',
                `services[${serviceIndex}][quantity]`
            )
            .val(1);


        newRow.find('.service-unit-price')
            .attr(
                'name',
                `services[${serviceIndex}][unit_price]`
            )
            .val(0);


        newRow.find('.service-subtotal')
            .val('0.00');


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

});