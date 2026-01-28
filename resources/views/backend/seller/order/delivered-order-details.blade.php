@extends('backend.seller.seller-master')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0"><i class='fas fa-hand-point-right'></i> Manage Delivered Orders</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Delivered Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        @php
            $footercontent = Helper::getfootercontacts();
        @endphp

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Delivered Order Details</h3>
                                <a class="btn btn-sm btn-secondary float-right"
                                    href="{{ route('seller.orders.delivered.list') }}">
                                    <i class="fas fa-list"></i> Delivered Orders List
                                </a>
                            </div>

                            <div class="card-body p-0">
                                <!-- Invoice Container -->
                                <div class="invoice p-4 p-md-5 bg-white">
                                    <!-- Header -->
                                    <div class="row mb-5">
                                        <div class="col-md-4 text-center text-md-left">
                                            <img src="{{ asset('frontend/assets/images/12345.png') }}" alt="Company Logo"
                                                class="img-fluid" style="max-height: 130px;">
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h2 class="font-weight-bold mb-2">U Super Shop</h2>
                                            <p class="mb-0">
                                                <strong>Whatsapp:</strong> {{ $footercontent->mobile ?? 'N/A' }}<br>
                                                <strong>Email:</strong> {{ $footercontent->email ?? 'N/A' }}<br>
                                                <strong>Address:</strong> {{ $footercontent->address ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-center text-md-right">
                                            @if (!empty($order->shop_id))
                                                <p class="mb-1"><strong>Shop ID:</strong> {{ $order->shop_id }}</p>
                                            @endif
                                            <h4 class="mb-1"><strong>Order No:</strong> ODR-#{{ $order->order_no }}</h4>
                                            <p class="mb-0"><strong>Status:</strong> <span
                                                    class="badge badge-success">Delivered</span></p>
                                        </div>
                                    </div>

                                    <hr class="my-5">

                                    <!-- Customer Information -->
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <h5 class="font-weight-bold mb-3">Billing / Shipping Address</h5>
                                            <p class="mb-1">
                                                <strong>Name:</strong> {{ $order['shipping']['name'] ?? 'N/A' }}<br>
                                                <strong>Mobile:</strong> {{ $order['shipping']['mobile'] ?? 'N/A' }}<br>
                                                <strong>Email:</strong> {{ $order['shipping']['email'] ?? 'N/A' }}<br>
                                                <strong>Address:</strong> {{ $order['shipping']['address'] ?? 'N/A' }}<br>
                                                <strong>Payment Method:</strong>
                                                {{ $order['payment']['payment_method'] ?? 'N/A' }}
                                                @if ($order['payment']['payment_method'] == 'Bkash')
                                                    <br><strong>Transaction ID:</strong>
                                                    {{ $order['payment']['transaction_no'] ?? 'N/A' }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Order Items Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center" style="width: 50px; min-width: 50px;">#</th>
                                                    <th style="min-width: 200px;">Product</th>
                                                    <th class="text-center" style="width: 120px; min-width: 120px;">Image
                                                    </th>
                                                    <th class="text-center" style="min-width: 120px;">Color / Size</th>
                                                    <th class="text-center" style="width: 70px; min-width: 70px;">Qty</th>
                                                    <th class="text-center" style="width: 120px; min-width: 120px;">Unit
                                                        Price<br>(Tk.)</th>
                                                    <th class="text-center" style="width: 130px; min-width: 130px;">
                                                        Total<br>(Tk.)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order['order_details'] as $index => $details)
                                                    @php
                                                        $sub_total = $details->quantity * $details->sell_price;
                                                    @endphp
                                                    <tr>
                                                        <td  style="width: 50px; min-width: 50px;">{{ $loop->iteration }}</td>
                                                        <td style="min-width: 200px;">{{ $details['product']['name'] }}</td>
                                                        <td class="text-center" style="width: 120px; min-width: 120px;">
                                                            <img src="{{ url('upload/product_images/' . $details['product']['image']) }}"
                                                                alt="Product" class="img-thumbnail"
                                                                style="max-width: 90px; max-height: 70px;">
                                                        </td>
                                                        <td class="text-center"  style="min-width: 120px;">
                                                            {{ $details->color_name ?? 'N/A' }} /
                                                            {{ $details->size_name ?? 'N/A' }}
                                                        </td>
                                                        <td class="text-center"  style="width: 70px; min-width: 70px;">{{ $details->quantity }}</td>
                                                        <td class="text-right"  style="width: 120px; min-width: 120px;">{{ number_format($details->sell_price, 2) }}
                                                        </td>
                                                        <td class="text-right" style="width: 130px; min-width: 130px;">{{ number_format($sub_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="font-weight-bold bg-light">
                                                <tr>
                                                    <td colspan="6" class="text-right">Subtotal:</td>
                                                    <td class="text-right">{{ number_format($order->order_total, 2) }} Tk.
                                                    </td>
                                                </tr>
                                                @if ($order->coupon_discount > 0)
                                                    <tr>
                                                        <td colspan="6" class="text-right">Coupon Discount:</td>
                                                        <td class="text-right">-
                                                            {{ number_format($order->coupon_discount, 2) }} Tk.</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td colspan="6" class="text-right">Delivery Charge:</td>
                                                    <td class="text-right">
                                                        {{ number_format($order->delivery_charge ?? 0, 2) }} Tk.</td>
                                                </tr>
                                                @php
                                                    $grand_total = round(
                                                        $order->order_total -
                                                            ($order->coupon_discount ?? 0) +
                                                            ($order->delivery_charge ?? 0),
                                                    );
                                                @endphp
                                                <tr class="bg-success text-white">
                                                    <td colspan="6" class="text-right">
                                                        <h5 class="mb-0">Grand Total:</h5>
                                                    </td>
                                                    <td class="text-right">
                                                        <h5 class="mb-0">{{ number_format($grand_total, 2) }} Tk.</h5>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Thank you note -->
                                    <div class="row mt-5">
                                        <div class="col-12 text-center text-muted">
                                            <p class="mb-0">Thank you for selling with U Super Shop!</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Print Button (Hidden on Print) -->
                                <div class="px-4 pb-4 no-print">
                                    <button onclick="window.print()" class="btn btn-info btn-sm float-right">
                                        <i class="fas fa-print"></i> Print Invoice
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .invoice,
            .invoice * {
                visibility: visible;
            }

            .invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .card-header,
            .no-print,
            .content-header,
            .main-sidebar,
            .navbar {
                display: none !important;
            }

            .content-wrapper {
                background: white !important;
                margin: 0 !important;
            }

            body {
                padding: 0 !important;
            }
        }

        .invoice {
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            background: #fff;
        }
/* Better scrollbar on mobile */
        .table-responsive {
            border-radius: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive::-webkit-scrollbar {
            height: 10px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush
