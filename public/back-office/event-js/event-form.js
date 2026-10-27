/*
 * Event / Order wizard JavaScript.
 * Supports both separate Blade pages:
 *  - Event wizard: shared customer/event fields + initial orders[0][...]
 *  - Order wizard: one order using package_id/items/services/payment[...] keys
 * Requires jQuery (and Bootstrap/Select2 only if the host layout uses them).
 */
(function ($) {
    'use strict';

    $(function () {
        const $steps = $('.wizard-step');
        const $tabs = $('#eventWizardTabs [data-step]');
        if (!$steps.length) return;

        let currentStep = Number($steps.filter(':not(.d-none)').first().data('step-content')) || 1;
        const maxStep = Math.max.apply(null, $steps.map(function () {
            return Number($(this).data('step-content')) || 0;
        }).get());

        function showStep(step) {
            step = Math.max(1, Math.min(maxStep, Number(step) || 1));
            currentStep = step;
            $steps.addClass('d-none').filter('[data-step-content="' + step + '"]').removeClass('d-none');
            $tabs.removeClass('active').filter('[data-step="' + step + '"]').addClass('active');
            updatePreview();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        $(document).on('click', '.next-step', function () {
            showStep($(this).data('next') || currentStep + 1);
        });
        $(document).on('click', '.previous-step', function () {
            showStep($(this).data('previous') || currentStep - 1);
        });
        $tabs.on('click', function () { showStep($(this).data('step')); });

        function money(value) {
            const n = Number(value);
            return (Number.isFinite(n) ? n : 0).toLocaleString('en-PK', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
        }
        function num($el) {
            const n = Number($el.val());
            return Number.isFinite(n) ? n : 0;
        }
        function safe(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[c];
            });
        }
        function selectedText(selector) {
            const $el = $(selector);
            return $el.length ? ($el.find('option:selected').text() || '').trim() : '';
        }
        function setText(selector, value) { $(selector).text(value == null ? '' : value); }

        function recalculateRow($row, isService) {
            const qtySelector = isService ? '.service-quantity' : '.quantity';
            const priceSelector = isService ? '.service-unit-price' : '.unit-price';
            const subtotalSelector = isService ? '.service-subtotal' : '.item-subtotal';
            const total = Math.max(0, num($row.find(qtySelector).first())) * Math.max(0, num($row.find(priceSelector).first()));
            const $subtotal = $row.find(subtotalSelector).first();
            if ($subtotal.is('input')) $subtotal.val(total.toFixed(2));
            else $subtotal.text(money(total));
            return total;
        }

        function calculateTotals() {
            let inventoryTotal = 0;
            $('.inventory-row').each(function () { inventoryTotal += recalculateRow($(this), false); });
            let servicesTotal = 0;
            $('.service-row').each(function () { servicesTotal += recalculateRow($(this), true); });

            const $package = $('#package_id');
            const $option = $package.find('option:selected');
            const packagePrice = $option.length ? (Number($option.data('price')) || 0) : 0;
            // The payment discount field is the single discount applied to this order.
            // Do not add the package's informational/default discount a second time.
            const manualDiscount = Math.max(0, num($('#discount')));
            const discountTotal = manualDiscount;
            const packageNet = packagePrice;
            const subtotal = packagePrice + inventoryTotal + servicesTotal;
            const grandTotal = Math.max(0, subtotal - manualDiscount);
            const advance = Math.max(0, num($('#advance_amount')));
            const remaining = Math.max(0, grandTotal - advance);

            setText('#inventory-items-total', money(inventoryTotal));
            setText('#inventory-total', money(inventoryTotal));
            setText('#services-total', money(servicesTotal));
            setText('#payment-package-total', money(packagePrice));
            setText('#payment-inventory-total', money(inventoryTotal));
            setText('#payment-services-total', money(servicesTotal));
            setText('#payment-discount', money(discountTotal));
            setText('#grand-total', money(grandTotal));
            setText('#payment-advance', money(advance));
            setText('#remaining-total', money(remaining));
            if ($('#subtotal').length) $('#subtotal').val(subtotal.toFixed(2));

            setText('#preview-package-total', money(packageNet));
            setText('#preview-inventory-total', money(inventoryTotal));
            setText('#preview-services-total', money(servicesTotal));
            setText('#preview-discount', money(discountTotal));
            setText('#preview-grand-total', money(grandTotal));
            setText('#preview-security-deposit', money(num($('#security_deposit'))));
            setText('#preview-advance', money(advance));
            setText('#preview-remaining', money(remaining));
            return { packagePrice, inventoryTotal, servicesTotal, discountTotal, grandTotal, advance, remaining };
        }

        function updatePackagePreview() {
            const $option = $('#package_id option:selected');
            const hasPackage = !!$option.val();
            $('#package-details').toggleClass('d-none', !hasPackage);
            if (!hasPackage) {
                setText('#package-detail-name', 'Custom Event');
                setText('#package-detail-price', money(0));
                setText('#package-discount', money(0));
                setText('#package-detail-description', '');
                $('#package-detail-includes, #preview-package-includes').empty();
                setText('#preview-package-name', 'Custom Event');
                setText('#preview-package-price', money(0));
                setText('#preview-package-discount', money(0));
                setText('#preview-package-description', '');
                $('#preview-package-section').hide();
                return;
            }
            const name = $option.data('name') || $option.text().trim();
            const price = Number($option.data('price')) || 0;
            const discount = Number($option.data('discount')) || 0;
            const description = $option.data('description') || '';
            setText('#package-detail-name', name);
            setText('#package-detail-price', money(price));
            setText('#package-discount', money(discount));
            setText('#package-detail-description', description);
            setText('#preview-package-name', name);
            setText('#preview-package-price', money(price));
            setText('#preview-package-discount', money(discount));
            setText('#preview-package-description', description);
            $('#preview-package-section').show();

            let includes = [];
            try { includes = JSON.parse($option.attr('data-inventory') || '[]'); } catch (e) { includes = []; }
            const rows = includes.map(function (item) {
                return '<tr><td>' + safe(item.category || '-') + '</td><td>' + safe(item.item || '-') + '</td><td>' + safe(item.quantity || 0) + '</td></tr>';
            }).join('');
            $('#package-detail-includes, #preview-package-includes').html(rows || '<tr><td colspan="3" class="text-muted">No package inventory included.</td></tr>');
        }

        function updatePreview() {
            const customerName = $('#name').val() || '';
            setText('#preview-customer-name', customerName);
            setText('#preview-cnic', $('#cnic_no').val() || '');
            setText('#preview-phone', $('#phone').val() || '');
            setText('#preview-alternate-phone', $('#alter_phone').val() || '');
            setText('#preview-address', $('#address').val() || '');
            setText('#preview-event-category', selectedText('#event_category_id'));
            setText('#preview-start-date', $('#start_date').val() || '');
            setText('#preview-end-date', $('#end_date').val() || '');
            setText('#preview-venue', $('#venue').val() || '');

            const inventoryRows = [];
            $('.inventory-row').each(function () {
                const $row = $(this);
                const category = $row.find('.inventory-category option:selected').text().trim();
                const item = $row.find('.inventory-item option:selected').text().trim();
                const qty = num($row.find('.quantity').first());
                const price = num($row.find('.unit-price').first());
                if ($row.find('.inventory-item').val()) inventoryRows.push('<tr><td>' + safe(category) + '</td><td>' + safe(item) + '</td><td>' + qty + '</td><td>' + money(price) + '</td><td>' + money(qty * price) + '</td></tr>');
            });
            $('#preview-inventory-items').html(inventoryRows.join(''));
            $('#preview-inventory-section').toggle(inventoryRows.length > 0);

            const serviceRows = [];
            $('.service-row').each(function () {
                const $row = $(this);
                const service = $row.find('.service option:selected').text().trim();
                const qty = num($row.find('.service-quantity').first());
                const price = num($row.find('.service-unit-price').first());
                if ($row.find('.service').val()) serviceRows.push('<tr><td>' + safe(service) + '</td><td>' + qty + '</td><td>' + money(price) + '</td><td>' + money(qty * price) + '</td></tr>');
            });
            $('#preview-services').html(serviceRows.join(''));
            $('#preview-services-section').toggle(serviceRows.length > 0);
            calculateTotals();
        }

        function renumberRows($container, rowSelector, collection) {
            $container.find(rowSelector).each(function (index) {
                $(this).find('[name]').each(function () {
                    const name = $(this).attr('name');
                    if (!name) return;
                    // Supports both simple items[0][field] and nested orders[0][items][0][field].
                    let updated = name.replace(/(items|services)\[\d+\]/, '$1[' + index + ']');
                    $(this).attr('name', updated);
                });
            });
        }

        function addRow(containerSelector, rowSelector, kind) {
            const $container = $(containerSelector);
            const $first = $container.find(rowSelector).first();
            if (!$first.length) return;
            const $clone = $first.clone(false, false);
            $clone.find('select').each(function () { $(this).val(''); });
            $clone.find('input').each(function () {
                const $input = $(this);
                if ($input.is('[type="number"]')) $input.val($input.hasClass('quantity') || $input.hasClass('service-quantity') ? 1 : 0);
                else $input.val('');
            });
            $clone.find('.unit-price, .service-unit-price').val('0');
            $clone.find('.item-subtotal, .service-subtotal').val('0.00');
            $container.append($clone);
            renumberRows($container, rowSelector, kind);
            if ($.fn.select2) $clone.find('.select2').removeClass('select2-hidden-accessible').next('.select2').remove();
            calculateTotals();
        }

        $(document).on('click', '#add-inventory', function () { addRow('#inventory-items', '.inventory-row', 'items'); });
        $(document).on('click', '#add-service', function () { addRow('#event-services', '.service-row', 'services'); });
        $(document).on('click', '.remove-inventory', function () {
            const $rows = $('#inventory-items .inventory-row');
            if ($rows.length > 1) $(this).closest('.inventory-row').remove();
            else $(this).closest('.inventory-row').find('select').val('').end().find('input').val('');
            renumberRows($('#inventory-items'), '.inventory-row', 'items');
            calculateTotals(); updatePreview();
        });
        $(document).on('click', '.remove-service', function () {
            const $rows = $('#event-services .service-row');
            if ($rows.length > 1) $(this).closest('.service-row').remove();
            else $(this).closest('.service-row').find('select').val('').end().find('input').val('');
            renumberRows($('#event-services'), '.service-row', 'services');
            calculateTotals(); updatePreview();
        });

        // Set a selected inventory item's unit price from its option metadata.
        $(document).on('change', '.inventory-item', function () {
            const $option = $(this).find('option:selected');
            $(this).closest('.inventory-row').find('.unit-price').val(Number($option.data('unit-price') || $option.data('price') || 0).toFixed(2));
            calculateTotals(); updatePreview();
        });
        $(document).on('change', '.service', function () {
            const $option = $(this).find('option:selected');
            $(this).closest('.service-row').find('.service-unit-price').val(Number($option.data('unit-price') || $option.data('price') || 0).toFixed(2));
            calculateTotals(); updatePreview();
        });
        $(document).on('input change', '.quantity, .unit-price, .service-quantity, .service-unit-price, #discount, #advance_amount, #security_deposit, #subtotal', function () {
            calculateTotals(); updatePreview();
        });
        $(document).on('change', '#package_id', function () { updatePackagePreview(); calculateTotals(); updatePreview(); });
        $(document).on('input change', '#name, #caste, #cnic_no, #phone, #alter_phone, #address, #event_category_id, #start_date, #end_date, #venue', updatePreview);

        // Populate inventory choices when a category changes, if the endpoint is configured.
        $(document).on('change', '.inventory-category', function () {
            const $category = $(this);
            const $row = $category.closest('.inventory-row');
            const $item = $row.find('.inventory-item');
            const url = $('#inventory-items').data('items-url');
            const categoryId = $category.val();
            if (!url || !categoryId) {
                $item.html('<option value="">Select Inventory Item</option>').prop('disabled', true);
                $row.find('.unit-price').val('0.00');
                calculateTotals(); updatePreview();
                return;
            }

            $item.prop('disabled', true).html('<option value="">Loading inventory...</option>');
            $.ajax({
                url: url,
                method: 'GET',
                data: { inventory_category_id: categoryId, category_id: categoryId }
            })
            .done(function (response) {
                // Support a plain array and common Laravel successResponse wrappers.
                let items = response;
                for (let i = 0; i < 4 && items && !Array.isArray(items); i++) {
                    if (Array.isArray(items.data)) items = items.data;
                    else if (items.data && typeof items.data === 'object') items = items.data;
                    else if (Array.isArray(items.items)) items = items.items;
                    else break;
                }

                if (!Array.isArray(items)) {
                    console.error('Unexpected inventory-items response:', response);
                    $item.html('<option value="">Invalid inventory response</option>').prop('disabled', true);
                    return;
                }

                let html = '<option value="">Select Inventory Item</option>';
                items.forEach(function (item) {
                    const id = item.id || item.ulid || '';
                    const label = item.name || item.title || '';
                    const price = item.price_per_unit != null ? item.price_per_unit : (item.price != null ? item.price : 0);
                    html += '<option value="' + safe(id) + '" data-unit-price="' + safe(price) + '">' + safe(label) + '</option>';
                });
                $item.html(html).val('').prop('disabled', items.length === 0);
                $row.find('.unit-price').val('0.00');
                calculateTotals(); updatePreview();
            })
            .fail(function (xhr) {
                console.error('Failed to load inventory items:', xhr.status, xhr.responseText);
                $item.html('<option value="">Could not load inventory items</option>').prop('disabled', true);
            });
        });

        updatePackagePreview();
        calculateTotals();
        updatePreview();
        showStep(currentStep);
    });
})(jQuery);
