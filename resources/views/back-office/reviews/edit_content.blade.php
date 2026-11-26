
@method('PUT')

<div class="row g-3 mb-4">

    <!-- Customer Select -->
    <div class="col-12 col-md-6">
        <label for="customer_id" class="form-label fw-semibold">
            Customer
        </label>
        <select
            id="customer_id"
            name="customer_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->ulid }}"
                    {{ old('customer_id', $model->customer?->ulid) == $customer->ulid ? 'selected' : '' }}>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
        <span id="customer_id_error" class="text-danger error">{{ $errors->first('customer_id') }}</span>
    </div>

    <!-- Rating Select -->
    <div class="col-12 col-md-6">
        <label for="rating" class="form-label fw-semibold">
            Rating <span class="text-danger">*</span>
        </label>
        <select
            id="rating"
            name="rating"
            class="form-select form-select-lg"
        >
            <option value="">Select Rating</option>
            @foreach(range(1, 5) as $rating)
                <option value="{{ $rating }}"
                    {{ old('rating', $model->rating) == $rating ? 'selected' : '' }}>
                    {{ $rating }} Star{{ $rating > 1 ? 's' : '' }}
                </option>
            @endforeach
        </select>
        <span id="rating_error" class="text-danger error">{{ $errors->first('rating') }}</span>
    </div>

    <!-- Message Input -->
    <div class="col-12">
        <label for="message" class="form-label fw-semibold">
            Message
        </label>
        <textarea
            id="message"
            name="message"
            class="form-control form-control-lg"
            rows="4"
            placeholder="Enter review message"
        >{{ old('message', $model->message) }}</textarea>
        <span id="message_error" class="text-danger error">{{ $errors->first('message') }}</span>
    </div>

    <!-- Status Select -->
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>
        <select
            id="status_id"
            name="status_id"
            class="form-select form-select-lg"
        >
            <option value="">Select Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->ulid }}"
                    {{ old('status_id', $model->status?->ulid) == $status->ulid ? 'selected' : '' }}>
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>

</div>

<script>
    $(document).ready(function () {
        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width: '100%'
            });
        });
    });
</script>
