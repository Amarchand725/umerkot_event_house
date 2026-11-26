
<table class="table table-flush-spacing">

    <!-- Question -->
    <tr>
        <td class="text-nowrap fw-semibold">Question</td>
        <td>{{ $model?->question ?? '-' }}</td>
    </tr>

    <!-- Answer -->
    <tr>
        <td class="text-nowrap fw-semibold">Answer</td>
        <td>
            <div class="text-wrap">{!! nl2br(e($model?->answer ?? '-')) !!}</div>
        </td>
    </tr>

    <!-- Status -->
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @php
                $status = $model?->status;
            @endphp

            @if($status)
                <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                    {{ strtoupper($status->name) }}
                </span>
            @else
                -
            @endif
        </td>
    </tr>

    <!-- Author -->
    @if($author = $model?->author)
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

    <!-- Created At -->
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ $model?->created_at?->format('d, M Y | h:i A') ?? '-' }}</td>
    </tr>
</table>
