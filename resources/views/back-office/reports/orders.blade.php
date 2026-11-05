@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Order Reports</h4>
            <p class="text-muted mb-0">Order-wise financial records</p>
        </div>

        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </div>

    @include('reports.partials.filters')

    <div class="card border shadow-none">
        <div class="card-header">
            <h5 class="mb-0">Order Records</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Event</th>
                        <th>Customer</th>
                        <th>Grand Total</th>
                        <th>Discount</th>
                        <th>Payable</th>
                        <th>Security Deposit</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $loop->index }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->event->event_number ?? '-' }}</td>
                            <td>{{ $order->event->customer->name ?? '-' }}</td>
                            <td>{{ number_format($order->grand_total, 2) }}</td>
                            <td>{{ number_format($order->discount, 2) }}</td>
                            <td>{{ number_format($order->payable_amount, 2) }}</td>
                            <td>{{ number_format($order->security_deposit, 2) }}</td>
                            <td>{{ $order->status->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                No order records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
