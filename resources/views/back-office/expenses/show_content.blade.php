<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Event</td>
        <td>{{ $model?->event?->name ?? '-' }}</td>
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
        <td>{{ number_format($model?->amount ?? 0, 2) }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Reference</td>
        <td>{{ $model?->reference ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Expense Date</td>
        <td>{{ $model?->expense_date?->format('d M Y') ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>{{ $model?->description ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @php
                $status = $model?->status;
            @endphp

            @if ($status)
                <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                    {{ strtoupper($status->name) }}
                </span>
            @else
                -
            @endif
        </td>
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
        <td>{{ $model->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>
</table>
