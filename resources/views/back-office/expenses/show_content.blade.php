<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Event</td>
        <td>
            {{ $model?->event?->customer?->name }} - ({{ $model?->event?->event_number }}) - {{ date('d M Y, h:i A', strtotime($model?->event?->start_date)) }}
        </td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Expense Category</td>
        <td>{{ $model?->expenseCategory?->name ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Payment Method</td>
        <td>{{ $model?->paymentMethod?->name ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Amount</td>
        <td>
            <span class="badge rounded-pill px-3 py-2 '. {{badgeClass('paid')}} .'">
                Rs.{{ $model?->amount ?? 0 }}
            </span>
        </td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Reference</td>
        <td>{{ $model?->reference ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Expense Date</td>
        <td>
            <div class="fw-semibold">{{ $model?->expense_date ? date('d M Y', strtotime($model->expense_date)) : '-' }}</div>
        </td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>{{ $model?->description ?? '-' }}</td>
    </tr>

    @if($author = $model->author)
    <tr>
        <td class="text-nowrap fw-semibold">Author</td>
        <td>
            @php
                $avatarPath = optional($author->avatar)->path
                        ? asset('storage/' . $author->avatar->path)
                        : asset('back-office/assets/img/avatars/default-avatar.png');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $author->name ?? '-' }}</span>
                    <small class="text-muted">{{ $author->email ?? '-' }}</small>
                </div>
            </div>
        </td>
    </tr>
    @endif
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>
            <div class="fw-semibold">{{ $model?->created_at ? date('d M Y, h:i A', strtotime($model->created_at)) : '-' }}</div>
        </td>
    </tr>
</table>
