<div class="d-flex align-items-center gap-1">
    @foreach(range(1, 5) as $star)
        <i class="ti ti-star-filled {{ $star <= $rating ? 'text-warning' : 'text-muted' }}"></i>
    @endforeach
    <small class="ms-1 text-muted">({{ $rating }}/5)</small>
</div>
