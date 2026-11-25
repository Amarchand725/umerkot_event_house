/*
|--------------------------------------------------------------------------
| Select2 Initialization
|--------------------------------------------------------------------------
*/

$('.select2').each(function () {
    $(this).select2({
        dropdownParent: $(this).parent(),
        width: '100%'
    });
});


/*
|--------------------------------------------------------------------------
| Tab Navigation
|--------------------------------------------------------------------------
*/

function showTab(tabId) {
    const tabButton = document.getElementById(tabId);

    bootstrap.Tab.getOrCreateInstance(tabButton).show();
}


/*
|--------------------------------------------------------------------------
| Validate Current Tab
|--------------------------------------------------------------------------
*/

function validateTab(tabSelector) {

    let isValid = true;

    $(tabSelector)
        .find('input, select, textarea')
        .filter(':enabled')
        .each(function () {

            if (!this.checkValidity()) {

                this.reportValidity();

                isValid = false;

                return false;
            }

        });

    return isValid;
}


/*
|--------------------------------------------------------------------------
| Step 1: Employee Information
|--------------------------------------------------------------------------
*/

$('#nextStep1').on('click', function () {

    if (validateTab('#employee-information')) {
        showTab('compensation-tab');
    }

});

$('#previousStep2').on('click', function () {
    showTab('employee-tab');
});


/*
|--------------------------------------------------------------------------
| Step 2: Salary Compensation
|--------------------------------------------------------------------------
*/

$('#nextStep2').on('click', function () {

    if (validateTab('#compensation-information')) {
        showTab('reference-tab');
    }

});

$('#previousStep3').on('click', function () {
    showTab('compensation-tab');
});


/*
|--------------------------------------------------------------------------
| Compensation Fields
|--------------------------------------------------------------------------
*/

function toggleCompensationFields() {

    let payType = $('#pay_type').val();

    let wrappers = [
        '#basic_pay_wrapper',
        '#daily_rate_wrapper',
        '#hourly_rate_wrapper',
        '#commission_basis_wrapper',
        '#commission_type_wrapper',
        '#commission_rate_wrapper'
    ];

    // Hide and disable all conditional fields first.
    wrappers.forEach(function (wrapper) {

        $(wrapper).hide();

        $(wrapper).find(':input')
            .prop('disabled', true)
            .prop('required', false);

    });


    // Monthly Fixed
    if (payType === 'monthly') {

        $('#basic_pay_wrapper').show();

        $('#basic_pay')
            .prop('disabled', false)
            .prop('required', true);

    }


    // Daily Wage
    if (payType === 'daily') {

        $('#daily_rate_wrapper').show();

        $('#daily_rate')
            .prop('disabled', false)
            .prop('required', true);

    }


    // Hourly Wage
    if (payType === 'hourly') {

        $('#hourly_rate_wrapper').show();

        $('#hourly_rate')
            .prop('disabled', false)
            .prop('required', true);

    }


    // Commission Based
    if (payType === 'commission') {

        $('#commission_basis_wrapper, #commission_type_wrapper, #commission_rate_wrapper')
            .show();

        $('#commission_basis, #commission_type, #commission_rate')
            .prop('disabled', false)
            .prop('required', true);

    }


    // Fixed + Commission
    if (payType === 'hybrid') {

        $('#basic_pay_wrapper, #commission_basis_wrapper, #commission_type_wrapper, #commission_rate_wrapper')
            .show();

        $('#basic_pay, #commission_basis, #commission_type, #commission_rate')
            .prop('disabled', false)
            .prop('required', true);

    }

}


$('#pay_type').on('change', toggleCompensationFields);

toggleCompensationFields();


/*
|--------------------------------------------------------------------------
| Final Form Submission
|--------------------------------------------------------------------------
*/

$('#employeeForm').on('submit', function (e) {

    // Validate all steps before submitting.
    if (!validateTab('#employee-information')) {

        e.preventDefault();

        showTab('employee-tab');

        return false;
    }

    if (!validateTab('#compensation-information')) {

        e.preventDefault();

        showTab('compensation-tab');

        return false;
    }

    // Reference fields are optional.
    // If a reference is entered, require the full name and phone.
    let referenceName = $('#reference_name').val().trim();
    let referencePhone = $('#reference_phone').val().trim();
    let referenceAddress = $('#reference_address').val().trim();

    if (referenceName || referencePhone || referenceAddress) {

        $('#reference_name, #reference_phone').prop('required', true);

        if (!validateTab('#reference-information')) {

            e.preventDefault();

            showTab('reference-tab');

            return false;
        }

    }

    return true;

});

function initializeSelect2(element = document) {
    $(element).find('select').each(function () {
        const $select = $(this);

        if ($.fn.select2 && !$select.hasClass('select2-hidden-accessible')) {
            $select.select2({
                width: '100%',
                dropdownParent: $select.parent()
            });
        }
    });
}

// Image Preview
$('.image-preview-input').on('change', function () {
    const input = this;
    const previewId = $(this).data('preview');
    const preview = $('#' + previewId);

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (!file.type.startsWith('image/')) {
            input.value = '';
            preview.attr('src', '').addClass('d-none');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            preview.attr('src', e.target.result);
            preview.removeClass('d-none');
        };

        reader.readAsDataURL(file);
    } else {
        preview.attr('src', '').addClass('d-none');
    }
});
