@method('PUT')

<div class="row g-3 mb-4">

    {{-- Event --}}
    <div class="col-12 col-md-6">
        <label for="event_id" class="form-label fw-semibold">
            Event
        </label>

        <select id="event_id" name="event_id" class="form-select form-select-lg">
            <option value="">Select event</option>

            @foreach ($events as $event)
                <option
                    value="{{ $event->ulid }}"
                    {{ old('event_id', $model->event?->ulid) == $event->ulid ? 'selected' : '' }}
                >
                    {{ $event->name }}
                </option>
            @endforeach
        </select>

        <span id="event_id_error" class="text-danger error">
            {{ $errors->first('event_id') }}
        </span>
    </div>


    {{-- Expense Category --}}
    <div class="col-12 col-md-6">
        <label for="expense_category_id" class="form-label fw-semibold">
            Expense Category <span class="text-danger">*</span>
        </label>

        <select
            id="expense_category_id"
            name="expense_category_id"
            class="form-select form-select-lg"
        >
            <option value="">Select expense category</option>

            @foreach ($expenseCategories as $category)
                <option
                    value="{{ $category->ulid }}"
                    {{ old('expense_category_id', $model->expense_category?->ulid) == $category->ulid ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <span id="expense_category_id_error" class="text-danger error">
            {{ $errors->first('expense_category_id') }}
        </span>
    </div>


    {{-- Payment Method --}}
    <div class="col-12 col-md-6">
        <label for="payment_method_id" class="form-label fw-semibold">
            Payment Method
        </label>

        <select
            id="payment_method_id"
            name="payment_method_id"
            class="form-select form-select-lg"
        >
            <option value="">Select payment method</option>

            @foreach ($paymentMethods as $paymentMethod)
                <option
                    value="{{ $paymentMethod->ulid }}"
                    {{ old('payment_method_id', $model->payment_method?->ulid) == $paymentMethod->ulid ? 'selected' : '' }}
                >
                    {{ $paymentMethod->name }}
                </option>
            @endforeach
        </select>

        <span id="payment_method_id_error" class="text-danger error">
            {{ $errors->first('payment_method_id') }}
        </span>
    </div>


    {{-- Amount --}}
    <div class="col-12 col-md-6">
        <label for="amount" class="form-label fw-semibold">
            Amount <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="amount"
            name="amount"
            class="form-control form-control-lg"
            placeholder="Enter amount"
            step="0.01"
            min="0"
            value="{{ old('amount', $model->amount) }}"
        />

        <span id="amount_error" class="text-danger error">
            {{ $errors->first('amount') }}
        </span>
    </div>


    {{-- Reference --}}
    <div class="col-12 col-md-6">
        <label for="reference" class="form-label fw-semibold">
            Reference
        </label>

        <input
            type="text"
            id="reference"
            name="reference"
            class="form-control form-control-lg"
            placeholder="Invoice, receipt, transaction or PO number"
            value="{{ old('reference', $model->reference) }}"
        />

        <span id="reference_error" class="text-danger error">
            {{ $errors->first('reference') }}
        </span>
    </div>


    {{-- Expense Date --}}
    <div class="col-12 col-md-6">
        <label for="expense_date" class="form-label fw-semibold">
            Expense Date
        </label>

        <input
            type="date"
            id="expense_date"
            name="expense_date"
            class="form-control form-control-lg"
            value="{{ old('expense_date', $model->expense_date?->format('Y-m-d')) }}"
        />

        <span id="expense_date_error" class="text-danger error">
            {{ $errors->first('expense_date') }}
        </span>
    </div>


    {{-- Description --}}
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Description
        </label>

        <textarea
            id="description"
            name="description"
            class="form-control"
            rows="4"
            placeholder="Enter expense description"
        >{{ old('description', $model->description) }}</textarea>

        <span id="description_error" class="text-danger error">
            {{ $errors->first('description') }}
        </span>
    </div>


    {{-- Status --}}
    <div class="col-12">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>

        <select id="status_id" name="status_id" class="form-select">
            <option value="">Select status</option>

            @foreach ($statuses as $status)
                <option
                    value="{{ $status->ulid }}"
                    {{ old('status_id', $model->status_id) == $status->id ? 'selected' : '' }}
                >
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>

        <span id="status_id_error" class="text-danger error">
            {{ $errors->first('status_id') }}
        </span>
    </div>

</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
