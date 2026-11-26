@php
$attachmentPath = optional($attachment?->attachment)?->path
    ? asset('storage/' . $attachment?->attachment?->path)
    : asset('back-office/assets/img/avatars/default-avatar.png');
@endphp

<div class="d-flex align-items-center gap-2">
    <img src="{{ $attachmentPath }}" width="36" height="36" class="rounded-circle" alt="Attachment">
</div>
