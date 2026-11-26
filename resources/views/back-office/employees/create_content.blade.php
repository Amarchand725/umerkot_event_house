{{-- STEP NAVIGATION --}}
<div class="card-header border-bottom">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">

        <li class="nav-item">
            <button type="button"
                    class="nav-link active"
                    id="employee-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#employee-information">
                <i class="ti ti-user me-1"></i>
                1. Employee Information
            </button>
        </li>

        <li class="nav-item">
            <button type="button"
                    class="nav-link"
                    id="compensation-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#compensation-information">
                <i class="ti ti-wallet me-1"></i>
                2. Salary Compensation
            </button>
        </li>

        <li class="nav-item">
            <button type="button"
                    class="nav-link"
                    id="reference-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#reference-information">
                <i class="ti ti-users me-1"></i>
                3. Reference
            </button>
        </li>

    </ul>
</div>

<div class="card-body">
    <div class="tab-content">

        {{-- ================================================= --}}
        {{-- STEP 1: EMPLOYEE INFORMATION --}}
        {{-- ================================================= --}}

        <div class="tab-pane fade show active"
                id="employee-information">

            <div class="mb-4">
                <h5 class="mb-1">Employee Information</h5>
                <small class="text-muted">
                    Enter the employee's personal and contact details.
                </small>
            </div>

            <div class="row g-3 mb-4">

                {{-- Name --}}
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-semibold">
                        Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                            id="name"
                            name="name"
                            class="form-control form-control-lg"
                            value="{{ old('name') }}"
                            placeholder="Enter name"
                            required>

                    <span class="text-danger error">
                        {{ $errors->first('name') }}
                    </span>
                </div>

                {{-- Father Name --}}
                <div class="col-12 col-md-6">
                    <label for="father_name" class="form-label fw-semibold">
                        Father's Name
                    </label>

                    <input type="text"
                            id="father_name"
                            name="father_name"
                            class="form-control form-control-lg"
                            value="{{ old('father_name') }}"
                            placeholder="Enter father name">

                    <span class="text-danger error">
                        {{ $errors->first('father_name') }}
                    </span>
                </div>

                {{-- Caste --}}
                <div class="col-12 col-md-6">
                    <label for="caste" class="form-label fw-semibold">
                        Caste
                    </label>

                    <input type="text"
                            id="caste"
                            name="caste"
                            class="form-control form-control-lg"
                            value="{{ old('caste') }}"
                            placeholder="Enter caste">

                    <span class="text-danger error">
                        {{ $errors->first('caste') }}
                    </span>
                </div>

                {{-- Phone --}}
                <div class="col-12 col-md-6">
                    <label for="phone" class="form-label fw-semibold">
                        Phone <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                            id="phone"
                            name="phone"
                            class="form-control form-control-lg"
                            value="{{ old('phone') }}"
                            placeholder="Enter phone"
                            required>

                    <span class="text-danger error">
                        {{ $errors->first('phone') }}
                    </span>
                </div>

                {{-- Email --}}
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label fw-semibold">
                        Email
                    </label>

                    <input type="email"
                            id="email"
                            name="email"
                            class="form-control form-control-lg"
                            value="{{ old('email') }}"
                            placeholder="Enter email">

                    <span class="text-danger error">
                        {{ $errors->first('email') }}
                    </span>
                </div>

                {{-- Joining Date --}}
                <div class="col-12 col-md-6">
                    <label for="joining_date" class="form-label fw-semibold">
                        Joining Date <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="joining_date"
                        name="joining_date"
                        class="form-control form-control-lg"
                        placeholder="Select Start Date & Time"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        value="{{ old('joining_date', isset($model->joining_date) ? \Carbon\Carbon::parse($model->joining_date)->format('Y-m-d H:i') : '') }}"
                    >

                    <span class="text-danger error">
                        {{ $errors->first('joining_date') }}
                    </span>
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label for="address" class="form-label fw-semibold">
                        Address
                    </label>

                    <textarea id="address"
                                name="address"
                                rows="3"
                                class="form-control"
                                placeholder="Enter complete address">{{ old('address') }}</textarea>

                    <span class="text-danger error">
                        {{ $errors->first('address') }}
                    </span>
                </div>

                {{-- Avatar --}}
                <div class="col-12 col-md-4">
                    <label for="avatar" class="form-label fw-semibold">Avatar</label>

                    <input type="file"
                        id="avatar"
                        name="avatar"
                        class="form-control image-preview-input"
                        accept="image/*"
                        data-preview="avatar_preview">

                    <div class="mt-2">
                        <img id="avatar_preview"
                            src=""
                            class="img-thumbnail d-none"
                            style="width: 120px; height: 120px; object-fit: cover;">
                    </div>

                    <span class="text-danger error">
                        {{ $errors->first('avatar') }}
                    </span>
                </div>

                {{-- CNIC Front --}}
                <div class="col-12 col-md-4">
                    <label for="cnic_front" class="form-label fw-semibold">CNIC Front</label>

                    <input type="file"
                        id="cnic_front"
                        name="cnic_front"
                        class="form-control image-preview-input"
                        accept="image/*"
                        data-preview="cnic_front_preview">

                    <div class="mt-2">
                        <img id="cnic_front_preview"
                            src=""
                            class="img-thumbnail d-none"
                            style="width: 100%; height: 120px; object-fit: contain;">
                    </div>

                    <span class="text-danger error">
                        {{ $errors->first('cnic_front') }}
                    </span>
                </div>

                {{-- CNIC Back --}}
                <div class="col-12 col-md-4">
                    <label for="cnic_back" class="form-label fw-semibold">CNIC Back</label>

                    <input type="file"
                        id="cnic_back"
                        name="cnic_back"
                        class="form-control image-preview-input"
                        accept="image/*"
                        data-preview="cnic_back_preview">

                    <div class="mt-2">
                        <img id="cnic_back_preview"
                            src=""
                            class="img-thumbnail d-none"
                            style="width: 100%; height: 120px; object-fit: contain;">
                    </div>

                    <span class="text-danger error">
                        {{ $errors->first('cnic_back') }}
                    </span>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button"
                        class="btn btn-primary"
                        id="nextStep1">
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>

        </div>


        {{-- ================================================= --}}
        {{-- STEP 2: SALARY COMPENSATION --}}
        {{-- ================================================= --}}

        <div class="tab-pane fade"
                id="compensation-information">

            <div class="mb-4">
                <h5 class="mb-1">Salary & Compensation</h5>
                <small class="text-muted">
                    Define the employee's initial salary and payment structure.
                </small>
            </div>

            <div class="row g-3 mb-4">

                {{-- Designation --}}
                <div class="col-12 col-md-6">
                    <label for="designation_id" class="form-label fw-semibold">
                        Designation
                    </label>

                    <select name="compensation[designation_id]"
                            id="designation_id"
                            class="form-select form-select-lg select2">
                        <option value="">Select Designation</option>

                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}"
                                @selected(old('designation_id') == $designation->id)>
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.designation_id') }}
                    </span>
                </div>

                {{-- Pay Type --}}
                <div class="col-12 col-md-6">
                    <label for="pay_type" class="form-label fw-semibold">
                        Salary Type <span class="text-danger">*</span>
                    </label>

                    <select name="compensation[pay_type]"
                            id="pay_type"
                            class="form-select form-select-lg"
                            required>
                        <option value="">Select Salary Type</option>

                        @foreach($pay_types as $type)
                            <option value="{{ $type->value }}"
                                @selected(old('compensation.pay_type') == $type->value)>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.pay_type') }}
                    </span>
                </div>

                {{-- Basic Pay --}}
                <div class="col-12 col-md-6" id="basic_pay_wrapper">
                    <label for="basic_pay" class="form-label fw-semibold">
                        Basic Pay (PKR)
                    </label>

                    <input type="number"
                            step="0.01"
                            min="0"
                            name="compensation[basic_pay]"
                            id="basic_pay"
                            class="form-control form-control-lg"
                            value="{{ old('basic_pay') }}"
                            placeholder="Enter monthly basic pay">

                    <span class="text-danger error">
                        {{ $errors->first('compensation.basic_pay') }}
                    </span>
                </div>

                {{-- Daily Rate --}}
                <div class="col-12 col-md-6" id="daily_rate_wrapper">
                    <label for="daily_rate" class="form-label fw-semibold">
                        Daily Rate (PKR)
                    </label>

                    <input type="number"
                            step="0.01"
                            min="0"
                            name="compensation[daily_rate]"
                            id="daily_rate"
                            class="form-control form-control-lg"
                            value="{{ old('daily_rate') }}"
                            placeholder="Enter daily rate">

                    <span class="text-danger error">
                        {{ $errors->first('compensation.daily_rate') }}
                    </span>
                </div>

                {{-- Hourly Rate --}}
                <div class="col-12 col-md-6" id="hourly_rate_wrapper">
                    <label for="hourly_rate" class="form-label fw-semibold">
                        Hourly Rate (PKR)
                    </label>

                    <input type="number"
                            step="0.01"
                            min="0"
                            name="compensation[hourly_rate]"
                            id="hourly_rate"
                            class="form-control form-control-lg"
                            value="{{ old('hourly_rate') }}"
                            placeholder="Enter hourly rate">

                    <span class="text-danger error">
                        {{ $errors->first('compensation.hourly_rate') }}
                    </span>
                </div>

                {{-- Commission Basis --}}
                <div class="col-12 col-md-6"
                        id="commission_basis_wrapper">

                    <label for="commission_basis"
                            class="form-label fw-semibold">
                        Commission Basis
                    </label>

                    <select name="compensation[commission_basis]"
                            id="commission_basis"
                            class="form-select form-select-lg">
                        <option value="">Select Commission Basis</option>

                        @foreach ($commission_basises as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('commission_basis') == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.commission_basis') }}
                    </span>
                </div>

                {{-- Commission Type --}}
                <div class="col-12 col-md-6"
                        id="commission_type_wrapper">

                    <label for="commission_type"
                            class="form-label fw-semibold">
                        Commission Type
                    </label>

                    <select name="compensation[commission_type]"
                            id="commission_type"
                            class="form-select form-select-lg">
                        <option value="">Select Commission Type</option>

                        @foreach($compensation_types as $type)
                            <option value="{{ $type->value }}"
                                @selected(old('compensation.commission_type') == $type->value)>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.commission_type') }}
                    </span>
                </div>

                {{-- Commission Rate --}}
                <div class="col-12 col-md-6"
                        id="commission_rate_wrapper">

                    <label for="commission_rate"
                            class="form-label fw-semibold">
                        Commission Rate / Amount
                    </label>

                    <input type="number"
                            step="0.01"
                            min="0"
                            name="compensation[commission_rate]"
                            id="commission_rate"
                            class="form-control form-control-lg"
                            value="{{ old('commission_rate') }}"
                            placeholder="Enter commission rate">

                    <span class="text-danger error">
                        {{ $errors->first('compensation.commission_rate') }}
                    </span>
                </div>

                {{-- Effective From --}}
                <div class="col-12 col-md-6">
                    <label for="effective_from"
                            class="form-label fw-semibold">
                        Effective From <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                            name="compensation[effective_from]"
                            id="effective_from"
                            class="form-control form-control-lg"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            value="{{ old('effective_from', old('joining_date')) }}"
                            required>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.effective_from') }}
                    </span>
                </div>

                {{-- Compensation Notes --}}
                <div class="col-12">
                    <label for="note"
                            class="form-label fw-semibold">
                        Compensation Notes
                    </label>

                    <textarea name="compensation[note]"
                                id="note"
                                rows="3"
                                class="form-control"
                                placeholder="Enter compensation details">{{ old('note') }}</textarea>

                    <span class="text-danger error">
                        {{ $errors->first('compensation.note') }}
                    </span>
                </div>

            </div>

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-label-secondary"
                        id="previousStep2">
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button type="button"
                        class="btn btn-primary"
                        id="nextStep2">
                    Next
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- STEP 3: REFERENCE INFORMATION --}}
        {{-- ================================================= --}}

        <div class="tab-pane fade"
                id="reference-information">

            <div class="mb-4">
                <h5 class="mb-1">Reference Information</h5>
                <small class="text-muted">
                    Enter the details of the person who referred this employee.
                    This information is optional.
                </small>
            </div>

            <div class="row g-3 mb-4">

                {{-- Reference Full Name --}}
                <div class="col-12 col-md-6">
                    <label for="reference_name"
                            class="form-label fw-semibold">
                        Reference Full Name
                    </label>

                    <input type="text"
                            id="reference_name"
                            name="reference[full_name]"
                            class="form-control form-control-lg"
                            value="{{ old('reference[full_name]') }}"
                            placeholder="Enter reference full name">

                    <span class="text-danger error">
                        {{ $errors->first('reference.full_name') }}
                    </span>
                </div>

                {{-- Reference Phone --}}
                <div class="col-12 col-md-6">
                    <label for="reference_phone"
                            class="form-label fw-semibold">
                        Reference Phone
                    </label>

                    <input type="text"
                            id="reference_phone"
                            name="reference[phone]"
                            class="form-control form-control-lg"
                            value="{{ old('reference[phone]') }}"
                            placeholder="Enter reference phone">

                    <span class="text-danger error">
                        {{ $errors->first('reference.phone') }}
                    </span>
                </div>

                {{-- Reference Address --}}
                <div class="col-12">
                    <label for="reference_address"
                            class="form-label fw-semibold">
                        Reference Address
                    </label>

                    <textarea id="reference_address"
                                name="reference[address]"
                                rows="3"
                                class="form-control"
                                placeholder="Enter reference address">{{ old('reference[address]') }}</textarea>

                    <span class="text-danger error">
                        {{ $errors->first('reference.address') }}
                    </span>
                </div>

            </div>

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-label-secondary"
                        id="previousStep3">
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>

                <button type="submit"
                        class="btn btn-success">
                    <i class="ti ti-device-floppy me-1"></i>
                    Save Employee
                </button>

            </div>

        </div>

    </div>

</div>

{{-- ================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ================================================= --}}

<script src="{{ asset('back-office/employee-js/employee.js') }}"></script>
<script>
    $(document).ready(function(){
        initializeSelect2();

        flatpickr("#joining_date, #effective_from, #effective_to, #leaving_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            allowInput: true
        });

        // Compensation fields
        toggleCompensationFields();

        $('#pay_type').on('change', function () {
            toggleCompensationFields();
        });
    });
</script>
