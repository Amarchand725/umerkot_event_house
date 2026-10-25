<div class="row g-3 mb-4">
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Payable Amount</label>
            <input type="text" id="paymentPayable"
                    class="form-control" readonly>
        </div>

        <div class="col-md-4">
            <label class="form-label">Paid Amount</label>
            <input type="text" id="paymentPaid"
                    class="form-control" readonly>
        </div>

        <div class="col-md-4">
            <label class="form-label">Outstanding</label>
            <input type="text" id="paymentOutstanding"
                    class="form-control fw-bold" readonly>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Payment Method</label>

        <select name="payment_method_id" class="form-select select2">
            <option value="">Select Payment Method</option>

            @foreach($paymentMethods as $method)
                <option value="{{ $method->ulid }}">
                    {{ $method->name }}
                </option>
            @endforeach
        </select>

        <span id="payment_method_id_error" class="text-danger error"></span>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Payment Amount</label>
        <input type="number"
                name="amount"
                id="paymentAmount"
                class="form-control"
                min="0.01"
                step="0.01"
                placeholder="Enter payment amount">

        <span id="amount_error" class="text-danger error"></span>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Payment Date</label>
        <input type="datetime-local"
                name="paid_at"
                class="form-control"
                value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="payment_note"
                    class="form-control"
                    rows="3"
                    placeholder="Optional payment note"></textarea>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>