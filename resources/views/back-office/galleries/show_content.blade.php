
<table class="table table-flush-spacing">

    <!-- Title -->
    <tr>
        <td class="text-nowrap fw-semibold">Title</td>
        <td>{{ $model?->title ?? '-' }}</td>
    </tr>

    <!-- Event -->
    <tr>
        <td class="text-nowrap fw-semibold">Event</td>
        <td>{{ $model?->event?->title ?? '-' }}</td>
    </tr>

    <!-- Description -->
    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>
            <div class="text-wrap">
                {!! nl2br(e($model?->description ?? '-')) !!}
            </div>
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

    <!-- Gallery Images -->
    <tr>
        <td class="text-nowrap fw-semibold">Gallery Images</td>
        <td>
            @if($model?->attachments && $model->attachments->count())
                <div class="row g-3">
                    @foreach($model->attachments as $attachment)
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ asset('storage/galleries/' . $attachment->path) }}"
                               target="_blank">
                                <img
                                    src="{{ asset('storage/galleries/' . $attachment->path) }}"
                                    class="img-fluid rounded border"
                                    style="width:100%; height:120px; object-fit:cover;"
                                    alt="Gallery Image"
                                >
                            </a>
                        </div>
                    @endforeach
                </div>
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
                    <img src="{{ $avatarPath }}"
                         width="36"
                         height="36"
                         class="rounded-circle"
                         alt="Avatar">

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
