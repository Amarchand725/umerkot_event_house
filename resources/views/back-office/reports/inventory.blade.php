<x-app-layout>
    @section('title', ($title ?? '').' Reports - '. config('app.name', '-'))
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h4 class="mb-1">{{$title}} Reports</h4>
                <p class="text-muted mb-0">
                    Detailed {{$title}} history and records.
                </p>
            </div>

            <a href="{{ route('back-office.reports.index') }}"
                class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Back to Reports
            </a>
        </div>

        @include('back-office.reports.partials.filters')

        <div class="card border shadow-none">
            <div class="card-header">
                <h5 class="mb-0">{{$title}} Records</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Event Date</th>
                            <th>Orders</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration + $items->firstItem() - 1 }}</td>

                                <td>
                                    {{ $item?->customer?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item?->start_date
                                        ? \Carbon\Carbon::parse($item->start_date)->format('d M Y')
                                        : '-' }}
                                </td>

                                <td>{{ $item?->orders?->count() }}</td>

                                <td>
                                    {{ $item?->status->name ?? '-' }}
                                </td>

                                <td>
                                    {{-- Add existing event detail route here --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $items->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
