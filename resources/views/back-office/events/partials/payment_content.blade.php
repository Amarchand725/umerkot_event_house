<ul class="nav nav-tabs mb-4" id="eventPaymentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#ordersHistoryTab"
                type="button"
                role="tab">
            <i class="ti ti-shopping-cart me-1"></i>
            Orders History
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#paymentHistoryTab"
                type="button"
                role="tab">
            <i class="ti ti-history me-1"></i>
            Payment History
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#addPaymentTab"
                type="button"
                role="tab">
            <i class="ti ti-cash me-1"></i>
            Add Payment
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- Orders History --}}
    <div class="tab-pane fade" id="ordersHistoryTab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order Number</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Payable</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Outstanding</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        @php
                            $orderDiscount = (float) ($order->discount ?? 0);
                            $invoiceDiscount = (float) ($order->invoice?->discount ?? 0);
                            $combinedDiscount = $orderDiscount + $invoiceDiscount;

                            // Assumes grand_total is before both order and invoice discounts.
                            $payableAmount = max(
                                0,
                                (float) $order->grand_total - $combinedDiscount
                            );

                            $orderPaid = (float) (
                                $order->invoice?->payments
                                    ?->filter(fn ($payment) => $payment->status?->name === 'Completed')
                                    ->sum('amount') ?? 0
                            );

                            $orderOutstanding = max(0, $payableAmount - $orderPaid);
                        @endphp

                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td class="text-end">{{ number_format((float) $order->grand_total, 2) }}</td>
                            <td class="text-end">{{ number_format($combinedDiscount, 2) }}</td>
                            <td class="text-end">{{ number_format($payableAmount, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($orderPaid, 2) }}</td>
                            <td class="text-end text-danger fw-semibold">{{ number_format($orderOutstanding, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Event Total</td>
                        <td class="text-end">{{ number_format($totalOrderAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($totalDiscount, 2) }}</td>
                        <td class="text-end">{{ number_format($totalPayable, 2) }}</td>
                        <td class="text-end">{{ number_format($totalPaid, 2) }}</td>
                        <td class="text-end">{{ number_format($totalOutstanding, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="tab-pane fade" id="paymentHistoryTab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Payment Number</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_number }}</td>
                            <td>
                                {{ $payment->paid_at
                                    ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, h:i A')
                                    : '-' }}
                            </td>
                            <td>{{ $payment->invoice_id ? 'Order Invoice' : 'Event Direct' }}</td>
                            <td>{{ $payment->paymentMethod?->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 {{ badgeClass($payment->status?->name ?? '-') }}">
                                    {{ strtoupper($payment->status?->name ?? '-') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No payments received.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Payment --}}
    <div class="tab-pane fade show active" id="addPaymentTab" role="tabpanel">
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <label class="form-label">Grand Total</label>
                <input type="text"
                       id="paymentPayable"
                       class="form-control"
                       value="{{ number_format($totalOrderAmount, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Total Discount</label>
                <input type="text"
                       id="totalDiscountDisplay"
                       class="form-control"
                       value="{{ number_format($totalDiscount, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Paid Amount</label>
                <input type="text"
                       id="paymentPaid"
                       class="form-control"
                       value="{{ number_format($totalPaid, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Outstanding</label>
                <input type="text"
                       id="paymentOutstanding"
                       class="form-control fw-bold text-danger"
                       value="{{ number_format($totalOutstanding, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Payment Method</label>
                <select name="payment_method_id" class="form-select select2" required>
                    <option value="">Select Payment Method</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method->ulid }}">{{ $method->name }}</option>
                    @endforeach
                </select>
                <span id="payment_method_id_error" class="text-danger error"></span>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Transaction Discount</label>
                <input type="number"
                       name="discount"
                       id="transactionDiscount"
                       class="form-control"
                       min="0"
                       step="0.01"
                       max="{{ $totalOutstanding }}"
                       value="0"
                       @disabled($totalOutstanding <= 0)>
                <span id="discount_error" class="text-danger error"></span>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Payment Amount</label>
                <input type="number"
                       name="amount"
                       id="paymentAmount"
                       class="form-control"
                       min="0"
                       step="0.01"
                       max="{{ $totalOutstanding }}"
                       value="{{ number_format($totalOutstanding, 2, '.', '') }}"
                       @disabled($totalOutstanding <= 0)>
                <span id="amount_error" class="text-danger error"></span>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Payment Date</label>
                <input type="datetime-local"
                       name="paid_at"
                       class="form-control"
                       value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Note</label>
                <textarea name="payment_note"
                          class="form-control"
                          rows="3"
                          maxlength="1000"
                          placeholder="Optional payment note"></textarea>
            </div>

        </div>
    </div>
</div>

<script>
    $('#addPaymentTab select.select2').each(function () {
        $(this).select2({
            dropdownParent: $('#addPaymentTab'),
            width: '100%'
        });
    });

    $(document).on('input change', '#transactionDiscount', function () {
        const outstanding = Math.max(
            0,
            parseFloat($('#paymentOutstanding').val()) || 0
        );

        let discount = parseFloat($(this).val());

        if (!Number.isFinite(discount)) {
            discount = 0;
        }

        discount = Math.max(0, Math.min(discount, outstanding));

        $(this).val(discount.toFixed(2));

        const maxPayment = Math.max(0, outstanding - discount);
        const $paymentAmount = $('#paymentAmount');

        $paymentAmount.attr('max', maxPayment.toFixed(2));

        // Default to settling the remaining balance after discount.
        // The user can still lower this amount to make a partial payment.
        const currentPayment = parseFloat($paymentAmount.val()) || 0;

        if (currentPayment > maxPayment || currentPayment === outstanding) {
            $paymentAmount.val(maxPayment.toFixed(2));
        }
    });

    $(document).on('input change', '#paymentAmount', function () {
        const outstanding = Math.max(
            0,
            parseFloat($('#paymentOutstanding').val()) || 0
        );

        const discount = Math.max(
            0,
            parseFloat($('#transactionDiscount').val()) || 0
        );

        const maxPayment = Math.max(0, outstanding - discount);
        let amount = parseFloat($(this).val());

        if (!Number.isFinite(amount)) {
            amount = 0;
        }

        amount = Math.max(0, Math.min(amount, maxPayment));

        $(this).val(amount.toFixed(2));
    });
</script>
