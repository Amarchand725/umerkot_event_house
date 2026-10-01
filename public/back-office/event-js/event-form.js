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
    | Wizard
    |--------------------------------------------------------------------------
    */

    function showStep(step) {

        $('.wizard-step').addClass('d-none');

        $(`[data-step-content="${step}"]`)
            .removeClass('d-none');

        $('#eventWizardTabs .nav-link')
            .removeClass('active');

        $(`#eventWizardTabs .nav-link[data-step="${step}"]`)
            .addClass('active');

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
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
    | Tab Click
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
    | Calculate Totals
    |--------------------------------------------------------------------------
    */

    function calculateTotals() {

        let total = 0;
        let totalItems = 0;

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

            total += subtotal;

            // Total quantity of all inventory items
            totalItems += quantity;

        });


        const discount =
            parseFloat($('#discount').val()) || 0;

        const advance =
            parseFloat($('#advance_amount').val()) || 0;


        /*
        |--------------------------------------------------------------------------
        | Grand Total
        |--------------------------------------------------------------------------
        */

        const grandTotal =
            Math.max(total - discount, 0);


        /*
        |--------------------------------------------------------------------------
        | Remaining Amount
        |--------------------------------------------------------------------------
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
            .text(total.toFixed(2));

        $('#subtotal')
            .val(total.toFixed(2));


        /*
        |--------------------------------------------------------------------------
        | Payment Summary
        |--------------------------------------------------------------------------
        */

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

            const row =
                $(this).closest('.inventory-row');

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
    | Quantity / Unit Price
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

        const select =
            newRow.find('.inventory-item');

        select
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

        inventoryContainer.append(newRow);

        /*
        | Initialize Select2
        */

        select.show();

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

    $('#discount, #advance_amount').on('input', function () {
        calculateTotals();
    });


    /*
    |--------------------------------------------------------------------------
    | Initial Calculation
    |--------------------------------------------------------------------------
    */

    calculateTotals();

});