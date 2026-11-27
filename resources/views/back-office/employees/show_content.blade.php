<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Employee</td>
        <td>
            @php
                $avatarPath = optional($model->avatar)->path
                        ? asset('storage/' . $model->avatar->path)
                        : asset('back-office/assets/img/avatars/default-avatar.png');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $model->name ?? '-' }}</span>
                    <small class="text-muted">{{ $model->email ?? '-' }}</small>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Designation</td>
        <td>
            {{ $model->compensation?->designation ? $model->compensation?->designation?->name : '-'; }}
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
        <td class="text-nowrap fw-semibold">Joining Date</td>
        <td>
            @if($model?->joining_date)
                {{ date('d M y', strtotime($model?->joining_date)) ?? '-' }}
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Leave Date</td>
        <td>
            @if($model?->leave_date)
                {{ date('d M y', strtotime($model?->leave_date)) ?? '-' }}
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Note</td>
        <td>{{ $model?->note ?? '-' }}</td>
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
