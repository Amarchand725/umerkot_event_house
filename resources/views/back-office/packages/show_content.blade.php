<table class="table table-flush-spacing">

    {{-- Package Information --}}
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td>{{ $model?->name ?? '-' }}</td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Price</td>
        <td>
            @php
                $originalPrice = $model?->price ?? 0;
                $discount = $model?->discount ?? 0;
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
        </td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Discount</td>
        <td>
            <span class="badge bg-label-danger ms-2">
                {{ number_format($discount, 2) }} OFF
            </span>
        </td>
    </tr>

    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>{{ $model?->description ?? '-' }}</td>
    </tr>

    {{-- Status --}}
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

    {{-- Author --}}
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
                    <img
                        src="{{ $avatarPath }}"
                        width="36"
                        height="36"
                        class="rounded-circle"
                        alt="Avatar"
                    >

                    <div class="d-flex flex-column">
                        <span class="fw-bold">
                            {{ $author->name ?? '-' }}
                        </span>

                        <small class="text-muted">
                            {{ $author->email ?? '-' }}
                        </small>
                    </div>
                </div>
            </td>
        </tr>
    @endif

    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>
            {{ $model->created_at
                ? $model->created_at->format('d, M Y | h:i A')
                : '-' }}
        </td>
    </tr>

</table>

{{-- Package Inventory Items --}}
<div class="card border shadow-none mt-4">

    <div class="card-header">
        <h5 class="mb-0">
            <i class="ti ti-box me-1"></i>
            Package Inventory Items
        </h5>
    </div>

    <div class="card-body p-0">

        @if($model->inventoryItems->count())

            <div class="table-responsive">
                <table class="table table-bordered mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Inventory Category</th>
                            <th>Inventory Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($model->inventoryItems as $index => $item)
                            <?php 
                                $itemName = '-'; 
                            ?>
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                @if(!empty($item->inventory_item_id))
                                    <?php $itemName = $item->inventoryItem?->name ?? '-'; ?>
                                    <td>
                                        Inventory
                                    </td>
                                @elseif(!empty($item->service_id))
                                    <?php $itemName = $item->service?->name ?? '-'; ?>
                                    <td>
                                        Service
                                    </td>
                                @else
                                    <td>
                                        -
                                    </td>
                                @endif
                                <td>
                                    {{ $itemName }}
                                </td>

                                <td>
                                    {{ $item->quantity }}
                                </td>

                                <td>
                                    {{ number_format($item->unit_price ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ number_format(
                                        ($item->quantity ?? 0) * ($item->unit_price ?? 0),
                                        2
                                    ) }}
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">
                                Inventory Items Original Total
                            </th>
                            <th>
                                {{ number_format(
                                    $model->inventoryItems->sum(
                                        fn($item) =>
                                            ($item->quantity ?? 0) *
                                            ($item->unit_price ?? 0)
                                    ),
                                    2
                                ) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        @else

            <div class="p-4 text-center text-muted">
                No inventory items added to this package.
            </div>

        @endif

    </div>
</div>