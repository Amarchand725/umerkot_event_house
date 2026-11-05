@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Event Reports</h4>
            <p class="text-muted mb-0">Event-wise business records</p>
        </div>

        <a href="{{ route('reports.index') }}"
           class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </div>

    @include('reports.partials.filters')

    <div class="card border shadow-none">

        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Event Records</h5>
            <span class="text-muted">
                Total: {{ $events->total() }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event Number</th>
                        <th>Customer</th>
                        <th>Category</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Venue</th>
                        <th>Total Orders</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>{{ $events->firstItem() + $loop->index }}</td>
                            <td>{{ $event->event_number }}</td>
                            <td>{{ $event->customer->name ?? '-' }}</td>
                            <td>{{ $event->eventCategory->name ?? '-' }}</td>
                            <td>{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('d M Y') : '-' }}</td>
                            <td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('d M Y') : '-' }}</td>
                            <td>{{ $event->venue ?? '-' }}</td>
                            <td>{{ $event->orders->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No event records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $events->links() }}
        </div>

    </div>
</div>
@endsection
