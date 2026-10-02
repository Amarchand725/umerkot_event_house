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

        $('#eventWizardTabs .nav-link')
            .removeClass('active');

        $(`#eventWizardTabs .nav-link[data-step="${step}"]`)
            .addClass('active');

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // Refresh preview whenever step 6 is opened
        if (step == 6) {
            updatePreview();
        }

        calculateTotals();
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

        let inventoryTotal = 0;
        let totalItems = 0;

        /*
        | Inventory
        */

        $('.inventory-row').each(function () {

            const row = $(this);

            const quantity =
                parseFloat(
                    row.find('.quantity').val()
                ) || 0;

            const unitPrice =
                parseFloat(
                    row.find('.unit-price').val()
                ) || 0;

            const subtotal =
                quantity * unitPrice;

            row.find('.item-subtotal')
                .val(subtotal.toFixed(2));

            inventoryTotal += subtotal;

            totalItems += quantity;

        });


        /*
        | Services
        */

        let servicesTotal = 0;

        $('.service-row').each(function () {

            const row = $(this);

            const quantity =
                parseFloat(
                    row.find('.service-quantity').val()
                ) || 0;

            const unitPrice =
                parseFloat(
                    row.find('.service-unit-price').val()
                ) || 0;

            const subtotal =
                quantity * unitPrice;

            row.find('.service-subtotal')
                .val(subtotal.toFixed(2));

            servicesTotal += subtotal;

        });


        /*
        | Discount
        */

        const discount =
            parseFloat($('#discount').val()) || 0;


        /*
        | Advance
        */

        const advance =
            parseFloat($('#advance_amount').val()) || 0;


        /*
        | Security Deposit
        */

        const securityDeposit =
            parseFloat($('#security_deposit').val()) || 0;


        /*
        | Total Before Discount
        */

        const total =
            inventoryTotal + servicesTotal;


        /*
        | Grand Total
        */

        const grandTotal =
            Math.max(total - discount, 0);


        /*
        | Remaining
        */

        const remainingAmount =
            Math.max(grandTotal - advance, 0);


        /*
        |--------------------------------------------------------------------------
        | Inventory Summary
        |--------------------------------------------------------------------------
        */

        $('#inventory-items-total')
            .text(totalItems);

        $('#inventory-total')
            .text(inventoryTotal.toFixed(2));

        $('#subtotal')
            .val(total.toFixed(2));


        /*
        |--------------------------------------------------------------------------
        | Services Summary
        |--------------------------------------------------------------------------
        */

        $('#services-total')
            .text(servicesTotal.toFixed(2));


        /*
        |--------------------------------------------------------------------------
        | Payment Summary
        |--------------------------------------------------------------------------
        */

        $('#payment-inventory-total')
            .text(inventoryTotal.toFixed(2));

        $('#payment-services-total')
            .text(servicesTotal.toFixed(2));

        $('#payment-total')
            .text(total.toFixed(2));

        $('#payment-discount')
            .text(discount.toFixed(2));

        $('#grand-total')
            .text(grandTotal.toFixed(2));

        $('#payment-advance')
            .text(advance.toFixed(2));

        $('#remaining-total')
            .text(remainingAmount.toFixed(2));


        /*
        |--------------------------------------------------------------------------
        | Preview Summary
        |--------------------------------------------------------------------------
        */

        $('#preview-inventory-total')
            .text(inventoryTotal.toFixed(2));

        $('#preview-services-total')
            .text(servicesTotal.toFixed(2));

        $('#preview-discount')
            .text(discount.toFixed(2));

        $('#preview-grand-total')
            .text(grandTotal.toFixed(2));

        $('#preview-security-deposit')
            .text(securityDeposit.toFixed(2));

        $('#preview-advance')
            .text(advance.toFixed(2));

        $('#preview-remaining')
            .text(remainingAmount.toFixed(2));

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

    function updatePreview() {

        /*
        | Customer
        */

        $('#preview-customer-name')
            .text($('#customer_name').val() || '-');

        $('#preview-phone')
            .text($('#phone').val() || '-');

        $('#preview-cnic')
            .text($('#cnic').val() || '-');

        $('#preview-alternate-phone')
            .text($('#alternate_phone').val() || '-');

        $('#preview-address')
            .text($('#address').val() || '-');


        /*
        | Event
        */

        $('#preview-event-category')
            .text(
                $('#event_category_id option:selected').text() || '-'
            );

        $('#preview-start-date')
            .text($('#start_date').val() || '-');

        $('#preview-end-date')
            .text($('#end_date').val() || '-');

        $('#preview-venue')
            .text($('#venue').val() || '-');


        /*
        | Inventory Preview
        */

        const inventoryPreview =
            $('#preview-inventory-items');

        inventoryPreview.empty();

        let hasInventory = false;

        $('.inventory-row').each(function () {

            const row = $(this);

            const itemId =
                row.find('.inventory-item').val();

            if (!itemId) {
                return;
            }

            hasInventory = true;

            const category =
                row.find('.inventory-category option:selected')
                    .text();

            const item =
                row.find('.inventory-item option:selected')
                    .text();

            const quantity =
                parseFloat(
                    row.find('.quantity').val()
                ) || 0;

            const unitPrice =
                parseFloat(
                    row.find('.unit-price').val()
                ) || 0;

            const subtotal =
                quantity * unitPrice;


            inventoryPreview.append(`
                <tr>
                    <td>${category}</td>
                    <td>${item}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${unitPrice.toFixed(2)}</td>
                    <td>Rs. ${subtotal.toFixed(2)}</td>
                </tr>
            `);

        });


        if (!hasInventory) {

            inventoryPreview.html(`
                <tr>
                    <td
                        colspan="5"
                        class="text-center text-muted"
                    >
                        No inventory items selected.
                    </td>
                </tr>
            `);

        }


        /*
        |----------------------------------------------------------------------
        | Services Preview
        |----------------------------------------------------------------------
        */

        const servicesPreview =
            $('#preview-services');

        servicesPreview.empty();

        let hasServices = false;

        $('.service-row').each(function () {

            const row = $(this);

            const serviceId =
                row.find('.service').val();

            if (!serviceId) {
                return;
            }

            hasServices = true;

            const service =
                row.find('.service option:selected')
                    .text();

            const quantity =
                parseFloat(
                    row.find('.service-quantity').val()
                ) || 0;

            const unitPrice =
                parseFloat(
                    row.find('.service-unit-price').val()
                ) || 0;

            const subtotal =
                quantity * unitPrice;


            servicesPreview.append(`
                <tr>
                    <td>${service}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${unitPrice.toFixed(2)}</td>
                    <td>Rs. ${subtotal.toFixed(2)}</td>
                </tr>
            `);

        });


        if (!hasServices) {

            servicesPreview.html(`
                <tr>
                    <td
                        colspan="4"
                        class="text-center text-muted"
                    >
                        No services selected.
                    </td>
                </tr>
            `);

        }


        /*
        | Recalculate totals
        */

        calculateTotals();

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Calculation
    |--------------------------------------------------------------------------
    */

    calculateTotals();

});