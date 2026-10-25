<ul class="nav nav-tabs mb-4" id="eventPaymentTabs" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#ordersHistoryTab"
                type="button">
            <i class="ti ti-shopping-cart me-1"></i>
            Orders History
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#paymentHistoryTab"
                type="button">
            <i class="ti ti-history me-1"></i>
            Payment History
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#addPaymentTab"
                type="button">
            <i class="ti ti-cash me-1"></i>
            Add Payment
        </button>
    </li>

</ul>

<div class="tab-content">

    {{-- Orders History --}}
    <div class="tab-pane fade" id="ordersHistoryTab">

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
                            $orderPaid = $order->paidInvoice?->payable_amount ?? 0;
                            $payableAmount = $order->grand_total-$order->discount;

                            $orderOutstanding = max(
                                0,
                                $payableAmount - $orderPaid
                            );
                            
                        @endphp

                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td class="text-end">
                                {{ number_format($order->grand_total, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($order->discount, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($payableAmount, 2) }}
                            </td>
                            <td class="text-end text-success">
                                {{ number_format($orderPaid, 2) }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                {{ number_format($orderOutstanding, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Grand Total</td>
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
    <div class="tab-pane fade" id="paymentHistoryTab">

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
                            <td>
                                {{ $payment->invoice_id ? 'Order Invoice' : 'Event Direct' }}
                            </td>
                            <td>{{ $payment->paymentMethod?->name ?? '-' }}</td>
                            <td class="text-end">
                                {{ number_format($payment->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 ' {{ badgeClass($payment?->status?->name ?? '-') }} '">
                                    {{ strtoupper($payment?->status?->name ?? '-') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No payments received.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Add Payment --}}
    <div class="tab-pane fade show active" id="addPaymentTab">

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <label class="form-label">Payable Amount</label>
                <input type="text"
                       id="paymentPayable"
                       class="form-control"
                       value="{{ number_format($totalPayable, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Paid Amount</label>
                <input type="text"
                       id="paymentPaid"
                       class="form-control"
                       value="{{ number_format($totalPaid, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Outstanding</label>
                <input type="text"
                       id="paymentOutstanding"
                       class="form-control fw-bold text-danger"
                       value="{{ number_format($totalOutstanding, 2, '.', '') }}"
                       readonly>
            </div>

            <div class="col-md-4">
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

            <div class="col-md-4">
                <label class="form-label fw-semibold">Discount</label>

                <input type="number"
                    name="discount"
                    id="discountAmount"
                    class="form-control"
                    min="0"
                    step="0.01"
                    max="{{ $totalOutstanding }}"
                    value="0">

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
                          placeholder="Optional payment note"></textarea>
            </div>

        </div>

    </div>

</div>

<script>
    $('#eventPaymentTabs select.select2').select2({
        width: '100%'
    });

    $('#addPaymentTab select.select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
            width: '100%'
        });
    });

    $(document).on('input change', '#discountAmount', function () {

        let outstanding = parseFloat($('#paymentOutstanding').val()) || 0;
        let discount = parseFloat($(this).val()) || 0;

        // Prevent negative discount
        discount = Math.max(0, discount);

        // Discount cannot exceed outstanding amount
        discount = Math.min(discount, outstanding);

        // Calculate payment amount after discount
        let paymentAmount = Math.max(0, outstanding - discount);

        // Update payment amount
        $('#paymentAmount')
            .val(paymentAmount.toFixed(2))
            .attr('max', paymentAmount.toFixed(2));

    });
</script>