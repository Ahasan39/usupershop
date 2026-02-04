@extends('backend.dropshipper.dropshipper-master')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h5 class="m-0"><i class='fas fa-hand-point-right'></i> Manage Pending Orders</h5>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dropshipper.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Pending Orders</li>
                        </ol>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-md-12">
                        <!-- Custom tabs (Charts with tabs)-->
                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    Order Details Info
                                    <a class="btn btn-sm btn-primary float-right"
                                        href="{{ route('dropshipper.orders.pending.list') }}"><i class="fas fa-list"></i>
                                        Pending Order
                                        List</a>
                                </h5>
                            </div>
                            <!-- /.card-header -->
                            @php
                                $footercontent = Helper::getfootercontacts();
                            @endphp
                            <div class="card-body">
                                <table class="text-center myTable" border="1" width="100%">
                                    <tr>
                                        <td width="30%">
                                            <img src="{{ asset('frontend/assets/images/12345.png') }}" 
                                            style="color:blue;width:100%;max-width:220px;"
                                                alt="{{ $logo->name ?? '' }}">
                                        </td>
                                        <td width="40%">
                                            <h4><strong>U Super Shop</strong></h4>
                                            <span><strong>Mobile No : </strong>{{ $footercontent->mobile ?? ''}}</span><br>
                                            <span><strong>Email : </strong> {{ $footercontent->email ?? '' }}</span><br>
                                            <span>{{ $footercontent->address ?? ''  }}</span>
                                        </td>
                                        <td width="30%">
                                            <?php if(!empty($order->shop_id)){?>
                                            <strong>Shop ID : {{ $order->shop_id }}</strong> </br>
                                            <?php } ?>
                                            <strong>Order No : ODR-#{{ $order->order_no }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Billing/Shipping Info :</strong></td>
                                        <td colspan="2">
                                            <strong>Name : </strong> {{ $order['shipping']['name'] }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Mobile No : </strong> {{ $order['shipping']['mobile'] }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Email : </strong> {{ $order['shipping']['email'] }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Address : </strong> {{ $order['shipping']['address'] }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Payment Method : </strong>
                                            {{ $order['payment']['payment_method'] }}
                                           
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><strong>Order Details</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product name & Image</strong></td>
                                        <td><strong>Color & Size</strong></td>
                                        <td><strong>Quantity & Price</strong></td>
                                    </tr>
                                    @php
                                    $subtotal = 0;
                                @endphp
                                
                                @foreach ($order->order_details as $details)
                                    <tr>
                                        <td style="text-align: left;">
                                            @if ($details->product)
                                                <img style="width: 50px;height:30px; border: 1px solid #000;background: #fff;padding: 3px;margin: 3px;"
                                                    src="{{ url('upload/product_images/' . $details->product->image) }}" alt="Product Image">
                                                &nbsp;
                                                {{ $details->product->name }}
                                            @else
                                                No product information available
                                            @endif
                                        </td>
                                        <td>
                                            {{ $details->color_name ?? 'N/A' }} & {{ $details->size_name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if ($details->product)
                                                @php
                                                    $item_price = $details->sell_price;
                                                    $item_total = $details->quantity * $item_price;
                                                    $subtotal += $item_total;
                                                @endphp
                                                {{ $details->quantity }} X {{ $item_price }} = {{ $item_total }} Tk.
                                            @else
                                                0 Tk.
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                    <td colspan="2" style="text-align: right;"><strong>Sub Total : </strong></td>
                                    <td><strong>{{ $subtotal }} Tk.</strong></td>
                                </tr>
                                
                                @if ($order->coupon_discount != null)
                                    <tr>
                                        <td colspan="2" style="text-align: right;">Coupon Discount : </td>
                                        <td>{{ $order->coupon_discount }} Tk.</td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <td colspan="2" style="text-align: right;">Delivery Charge : </td>
                                    <td>{{ $order->delivery_charge }} Tk.</td>
                                </tr>
                                
                                @php
                                    $grandTotal = round($subtotal - ($order->coupon_discount ?? 0) + $order->delivery_charge);
                                @endphp
                                
                                <tr>
                                    <td colspan="2" style="text-align: right;"><strong>Grand Total : </strong></td>
                                    <td><strong>{{ $grandTotal }} Tk.</strong></td>
                                </tr>
                                
                                </table>
                                
                                <a style="margin-right: 5px;" id="print" class="btn btn-sm btn-info float-right mt-3"
                                    href="{{ route('dropshipper.orders.print', $order->id) }}"><i class="fas fa-print"></i>
                                    Print</a>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <!-- /.card -->
                    </section>
                    <!-- /.Left col -->
                </div>
                <!-- /.row (main row) -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
