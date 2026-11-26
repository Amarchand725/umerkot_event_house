<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Employee Name</td>
        <td>{{ ucfirst($model?->employee?->name ?? '-') }} ({{ ucfirst($model?->employee?->compensation?->designation?->name ?? '-') }})</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Payable Unit</td>
        <td>
            @if($model->payable_units == 1.00)
                <span class="badge rounded-pill px-3 py-2 bg-success text-white ">
                    Full Day
                </span>
            @elseif($model->payable_units == 0.5)
                <span class="badge rounded-pill px-3 py-2 bg-warning text-white ">
                    Half Day
                </span>
            @else
                <span class="badge rounded-pill px-3 py-2 bg-danger text-white ">
                    Unpaid
                </span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Worked Hours</td>
        <td>
            {{$model->worked_hours ?? '0'}}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Overtime Hours</td>
        <td>
            {{$model->overtime_hours ?? '0'}}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @php
                $status = $model?->status;
            @endphp
            <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                {{ strtoupper($status->name) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Note</td>
        <td>
            {{$model->notes ?? '-'}}
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
