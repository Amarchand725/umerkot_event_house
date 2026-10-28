@php
    $orders = $model->orders ?? collect();

    $securityDeposit = (float) ($model->orders->sum('security_deposit') ?? 0);
@endphp

<div class="container-fluid">

    {{-- Event Information --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Order Return & Inspection #{{ $model->event_number }}</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Event</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $model?->eventCategory?->name ?? $model->event_number }}"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Customer</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $model->customer->name ?? 'N/A' }}"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Return Date</label>
                    <input type="datetime-local"
                           name="returned_at"
                           class="form-control"
                           value="{{ $model->end_date }}"
                           required>
                </div>
            </div>
        </div>
    </div>

    {{-- Return Items --}}
    @foreach ($orders as $orderIndex => $order)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Order #{{ $order->order_number ?? $order->id }}
                </h5>

                <span class="badge bg-primary">
                    {{ $order->orderItems->count() }} Items
                </span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle return-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Issued Qty</th>
                                <th>Good Qty</th>
                                <th>Damaged Qty</th>
                                <th>Missing Qty</th>
                                <th>Inspection Notes</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($order->orderItems as $orderItem)
                                <tr class="return-item-row"
                                    data-issued="{{ (int) $orderItem->quantity }}">

                                    <td>
                                        @if($orderItem->service_id)
                                            {{ $orderItem->service->name ?? 'Item' }}
                                        @elseif($orderItem->package_id)
                                            {{ $orderItem->package->name ?? 'Item' }}
                                        @else
                                            {{ $orderItem->item->name ?? 'Item' }}
                                        @endif

                                        <input type="hidden"
                                               name="items[{{ $orderItem->id }}][order_id]"
                                               value="{{ $order->id }}">

                                        <input type="hidden"
                                               name="items[{{ $orderItem->id }}][order_item_id]"
                                               value="{{ $orderItem->id }}">

                                        <input type="hidden"
                                               name="items[{{ $orderItem->id }}][issued_quantity]"
                                               value="{{ (int) $orderItem->quantity }}">
                                    </td>

                                    <td class="issued-quantity">
                                        {{ (int) $orderItem->quantity }}
                                    </td>

                                    <td>
                                        <input type="number"
                                            name="items[{{ $orderItem->id }}][good_quantity]"
                                            class="form-control quantity-input good-quantity"
                                            min="0"
                                            max="{{ (int) $orderItem->quantity }}"
                                            step="1"
                                            value="{{ (int) $orderItem->quantity }}"
                                            required>
                                    </td>

                                    <td>
                                        <small class="text-muted d-block damage-calculation">
                                            {{ number_format((float) ($orderItem?->item?->damage_charge ?? 0), 2) }}
                                            × 0 = 0.00
                                        </small>

                                        <input type="number"
                                            name="items[{{ $orderItem->id }}][damaged_quantity]"
                                            class="form-control quantity-input damaged-quantity"
                                            min="0"
                                            max="{{ (int) $orderItem->quantity }}"
                                            step="1"
                                            value="0"
                                            data-damage-price="{{ $orderItem?->item?->damage_charge ?? 0 }}"
                                            required>
                                    </td>

                                    <td>
                                        <small class="text-muted d-block missing-calculation">
                                            {{ number_format((float) ($orderItem?->item?->replacement_charge ?? 0), 2) }}
                                            × 0 = 0.00
                                        </small>

                                        <input type="number"
                                            name="items[{{ $orderItem->id }}][missing_quantity]"
                                            class="form-control quantity-input missing-quantity"
                                            min="0"
                                            max="{{ (int) $orderItem->quantity }}"
                                            step="1"
                                            value="0"
                                            data-missing-price="{{ $orderItem?->item?->replacement_charge ?? 0 }}"
                                            required>
                                    </td>

                                    {{-- <td>
                                        <small>{{ $orderItem?->item?->damage_charge ?? 0 }}x{{ (int) $orderItem->quantity }}</small>
                                        <input type="number"
                                            name="items[{{ $orderItem->id }}][damaged_quantity]"
                                            class="form-control quantity-input damaged-quantity"
                                            min="0"
                                            max="{{ (int) $orderItem->quantity }}"
                                            step="1"
                                            value="0"
                                            data-damage-price="{{ $orderItem?->item?->damage_charge ?? 0 }}"
                                            required>
                                    </td>

                                    <td>
                                        <small>{{ $orderItem?->item?->replacement_charge ?? 0 }}x{{ (int) $orderItem->quantity }}</small>
                                        <input type="number"
                                            name="items[{{ $orderItem->id }}][missing_quantity]"
                                            class="form-control quantity-input missing-quantity"
                                            min="0"
                                            max="{{ (int) $orderItem->quantity }}"
                                            value="0"
                                            data-missing-price="{{ $orderItem?->item?->replacement_charge ?? 0 }}"
                                            step="1"
                                            required>
                                    </td> --}}

                                    <td>
                                        <textarea name="items[{{ $orderItem->id }}][inspection_notes]"
                                                  class="form-control"
                                                  rows="1"
                                                  placeholder="Optional notes"></textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Security Deposit Settlement --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Security Deposit Settlement</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Security Deposit Received (Rs.)</label>
                    <input type="number"
                        name="security_deposit_received"
                        id="security_deposit_received"
                        class="form-control"
                        value="{{ number_format($securityDeposit, 2, '.', '') }}"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Damage Deduction (Rs.)</label>
                    <input type="number"
                        name="damage_deduction"
                        id="damage_deduction"
                        class="form-control"
                        value="0.00"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Missing Item Deduction (Rs.)</label>
                    <input type="number"
                        name="missing_deduction"
                        id="missing_deduction"
                        class="form-control"
                        value="0.00"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Total Deduction (Rs.)</label>
                    <input type="number"
                        name="total_deduction"
                        id="total_deduction"
                        class="form-control"
                        value="0.00"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Refundable Deposit (Rs.)</label>
                    <input type="number"
                        name="refundable_deposit"
                        id="refundable_deposit"
                        class="form-control"
                        value="{{ number_format($securityDeposit, 2, '.', '') }}"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Additional Amount Due (Rs.)</label>
                    <input type="number"
                        name="additional_amount_due"
                        id="additional_amount_due"
                        class="form-control"
                        value="0.00"
                        min="0"
                        step="0.01"
                        readonly>
                </div>

            </div>

            <div class="alert alert-info mb-0">
                Damage and missing deductions are calculated automatically
                from the inspected quantities and applicable item charges.
                Any amount exceeding the security deposit is shown as
                Additional Amount Due. Final values must be verified by Laravel.
            </div>
        </div>
    </div>

    {{-- <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Security Deposit Settlement</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Security Deposit Received (Rs.)</label>
                    <input type="number"
                           name="security_deposit_received"
                           id="security_deposit_received"
                           class="form-control"
                           value="{{ $securityDeposit }}"
                           min="0"
                           step="0.01"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Damage Deduction (Rs.)</label>
                    <input type="number"
                           name="damage_deduction"
                           id="damage_deduction"
                           class="form-control"
                           value="0.00"
                           min="0"
                           step="0.01">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Missing Item Deduction (Rs.)</label>
                    <input type="number"
                           name="missing_deduction"
                           id="missing_deduction"
                           class="form-control"
                           value="0.00"
                           min="0"
                           step="0.01"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Total Deduction (Rs.)</label>
                    <input type="number"
                           name="total_deduction"
                           id="total_deduction"
                           class="form-control"
                           value="0.00"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Refundable Deposit (Rs.)</label>
                    <input type="number"
                           name="refundable_deposit"
                           id="refundable_deposit"
                           class="form-control"
                           value="{{ number_format($securityDeposit, 2, '.', '') }}"
                           readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Additional Amount Due (Rs.)</label>
                    <input type="number"
                           name="additional_amount_due"
                           id="additional_amount_due"
                           class="form-control"
                           value="0.00"
                           readonly>
                </div>
            </div>

            <div class="alert alert-info mb-0">
                Final deductions must be calculated and verified by the
                backend using approved damage and replacement charges.
                Browser-calculated values are only previews.
            </div>
        </div>
    </div> --}}

    {{-- Return Status --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Return Status & Notes</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="status_id" class="form-label">Inspection Status</label>
                    <select id="status_id" name="status_id" class="form-select">
                        <option value="">Select status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->ulid }}">{{ ucfirst($status->name) }}</option>
                        @endforeach
                    </select>
                    <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">General Notes</label>
                    <textarea name="notes"
                              class="form-control"
                              rows="3"
                              placeholder="Enter return or inspection notes"></textarea>
                </div>
            </div>

            <div id="return-validation-message"
                 class="alert alert-warning d-none">
                Please account for all issued quantities before completing
                the inspection.
            </div>
        </div>
    </div>

</div>

<script>


(function () {
    const rows = document.querySelectorAll('.return-item-row');

    const damageInput = document.getElementById('damage_deduction');
    const missingInput = document.getElementById('missing_deduction');
    const totalInput = document.getElementById('total_deduction');
    const depositInput = document.getElementById('security_deposit_received');
    const refundableInput = document.getElementById('refundable_deposit');
    const additionalInput = document.getElementById('additional_amount_due');

    const money = value => (Number(value) || 0).toFixed(2);

    function calculate(changedRow = null, changedField = null) {
        let damageTotal = 0;
        let missingTotal = 0;

        rows.forEach(row => {
            const issued = Number(row.dataset.issued) || 0;

            const goodInput = row.querySelector('.good-quantity');
            const damagedInput = row.querySelector('.damaged-quantity');
            const missingQtyInput = row.querySelector('.missing-quantity');

            let good = Math.max(0, Math.floor(Number(goodInput.value) || 0));
            let damaged = Math.max(0, Math.floor(Number(damagedInput.value) || 0));
            let missing = Math.max(0, Math.floor(Number(missingQtyInput.value) || 0));

            // Recalculate the remaining quantity without exceeding issued qty.
            if (row === changedRow && changedField === 'good') {
                good = Math.min(good, issued);
                damaged = Math.min(damaged, issued - good);
                missing = issued - good - damaged;
            } else if (row === changedRow && changedField === 'damaged') {
                damaged = Math.min(damaged, issued);
                missing = Math.min(missing, issued - damaged);
                good = issued - damaged - missing;
            } else if (row === changedRow && changedField === 'missing') {
                missing = Math.min(missing, issued);
                damaged = Math.min(damaged, issued - missing);
                good = issued - damaged - missing;
            } else {
                good = Math.min(good, issued);
                damaged = Math.min(damaged, issued - good);
                missing = Math.min(missing, issued - good - damaged);
                good = issued - damaged - missing;
            }

            goodInput.value = good;
            damagedInput.value = damaged;
            missingQtyInput.value = missing;

            const damageRate = Number(damagedInput.dataset.damagePrice) || 0;
            const replacementRate = Number(missingQtyInput.dataset.missingPrice) || 0;

            const damageAmount = damaged * damageRate;
            const missingAmount = missing * replacementRate;

            row.querySelector('.damage-calculation').textContent =
                `${money(damageRate)} × ${damaged} = ${money(damageAmount)}`;

            row.querySelector('.missing-calculation').textContent =
                `${money(replacementRate)} × ${missing} = ${money(missingAmount)}`;

            damageTotal += damageAmount;
            missingTotal += missingAmount;
        });

        // All items across all event orders.
        damageInput.value = money(damageTotal);
        missingInput.value = money(missingTotal);

        const totalDeduction = damageTotal + missingTotal;
        const deposit = Math.max(0, Number(depositInput.value) || 0);

        totalInput.value = money(totalDeduction);
        refundableInput.value = money(Math.max(0, deposit - totalDeduction));
        additionalInput.value = money(Math.max(0, totalDeduction - deposit));
    }

    rows.forEach(row => {
        row.querySelector('.good-quantity').addEventListener('input', function () {
            calculate(row, 'good');
        });

        row.querySelector('.damaged-quantity').addEventListener('input', function () {
            calculate(row, 'damaged');
        });

        row.querySelector('.missing-quantity').addEventListener('input', function () {
            calculate(row, 'missing');
        });
    });

    calculate();
})();

</script>
