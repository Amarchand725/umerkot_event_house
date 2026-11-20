@php
    $originalPrice = (float) ($model?->price ?? 0);
    $discount = (float) ($model?->discount ?? 0);

    $salePrice = max($originalPrice - $discount, 0);
@endphp

@if($discount > 0)
    <span class="text-muted text-decoration-line-through me-2">
        {{ number_format($originalPrice, 2) }}
    </span>

    <span class="fw-bold text-success">
        {{ number_format($salePrice, 2) }}
    </span>
@else
    <span class="fw-bold">
        {{ number_format($originalPrice, 2) }}
    </span>
@endif