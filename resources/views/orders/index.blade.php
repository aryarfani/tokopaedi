@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Payment Type</th>
                                <th>Total</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="text-center">{{ $orders->firstItem() + $loop->index }}</td>
                                    <td>{{ $order->code }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        @if ($order->status == 'pending')
                                            <span class="badge bg-warning">{{ Str::title($order->status) }}</span>
                                        @elseif ($order->status == 'paid')
                                            <span class="badge bg-success">{{ Str::title($order->status) }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ Str::title($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->midtrans_payment_type }}</td>
                                    <td>{{ $order->total_price }}</td>
                                    <td>{{ $order->created_at->format('H:i, d F Y') }}</td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="7" class="alert alert-danger">
                                        Data Order Belum Tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
