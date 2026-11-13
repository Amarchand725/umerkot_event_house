
@method('PUT')

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="ti ti-calendar-event me-1"></i>
            Select Event
        </h5>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Event</label>

                <select id="event_id"
                        name="event_id"
                        class="form-select select2">

                    <option value="">Select Event</option>

                    @foreach($events as $event)
                        <option value="{{ $event->ulid }}"
                            {{ $payment?->invoice?->event_id === $event->id ? 'selected' : '' }}>

                            {{ $event->event_number }}
                            -
                            {{ $event->customer?->name ?? 'N/A' }}
                            -
                            {{ $event->start_date
                                ? \Carbon\Carbon::parse($event->start_date)->format('d M Y')
                                : '' }}
                        </option>
                    @endforeach

                </select>

                <span id="event_id_error" class="text-danger error"></span>
            </div>
        </div>
    </div>
</div>

<div id="payment-content"></div>

<script>
    $(document).ready(function () {

        $('#event_id').select2({
            dropdownParent: $('#event_id').parent(),
        });

        function loadPaymentContent(eventId) {

            const container = $('#payment-content');

            if (!eventId) {
                container.empty();
                return;
            }

            $.ajax({
                url: "{{ route('back-office.payments.payment-content') }}",
                type: "GET",

                data: {
                    event_id: eventId,
                    payment_id: "{{ $payment->ulid }}"
                },

                beforeSend: function () {
                    container.html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Loading payment information...</p>
                        </div>
                    `);
                },

                success: function (response) {
                    container.html(response);
                },

                error: function (xhr) {
                    container.html(`
                        <div class="alert alert-danger">
                            Unable to load payment information.
                        </div>
                    `);
                }
            });
        }

        // Load existing payment content on edit page.
        loadPaymentContent($('#event_id').val());

        // Load content when event changes.
        $(document).on('change', '#event_id', function () {
            loadPaymentContent($(this).val());
        });

    });
</script>
