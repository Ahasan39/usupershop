@extends('frontend.layouts.master')
@section('title', 'Order Details | ' . config('app.name'))

@section('meta_description', 'View your order details securely at ' . config('app.name') . ' — track your orders, check status, and manage your purchases safely.')
@section('meta_keywords', 'Order Details, My Orders, Customer Account, Track Order, ' . config('app.name'))
@section('meta_author', config('app.name'))

@push('meta')
    {{-- Prevent indexing by search engines --}}
    <meta name="robots" content="noindex, nofollow">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Order Details - {{ config('app.name') }}" />
    <meta property="og:description" content="Securely view and track your orders at {{ config('app.name') }}. Manage your purchases safely." />
    <meta property="og:image" content="{{ asset('frontend/images/og-default.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Order Details - {{ config('app.name') }}">
    <meta name="twitter:description" content="Securely view and track your orders at {{ config('app.name') }}. Manage your purchases safely.">
    <meta name="twitter:image" content="{{ asset('frontend/images/og-default.jpg') }}">
@endpush

@section('content')
    <style type="text/css">
        .prof li {
            background: #1781BF;
            padding: 7px;
            margin: 3px;
            border-radius: 15px;
        }

        .prof li a {
            color: #fff;
            padding-left: 15px;
        }

        .myTable tr td {
            padding: 10px;
        }
        table{
            width:100%;
            overflow-x: auto;
            display: block;
        }
    </style>
    <!-- Title page -->
    {{-- <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('../frontend/images/bg-01.jpg');">
        <h2 class="ltext-105 cl0 txt-center">Order Details</h2>
    </section> --}}
    <div class="container">
        <div class="row" style="padding: 70px 0px 70px 0px;">
            <div class="col-md-2">
                <ul class="prof">
                    <li><a href="{{ route('dashboard') }}">My Profile</a></li>
                    <li><a href="{{ route('customer.password.change') }}">Password Change</a></li>
                    <li><a href="{{ route('customer.order.list') }}">My Orders</a></li>
                </ul>
            </div>
            <div class="col-md-10">
                <table class="text-center myTable" border="1" width="100%">
                    <tr>
                        <td width="30%">
                            <img style="color:blue; !important;" src="{{ asset('frontend/assets/images/12345.png') }}" alt="{{ $logo->name }}" width="250px">
                        </td>
                        <td width="40%">
                            <h4><strong>U Super Shop</strong></h4>
                            <span><strong>WhatsApp No : </strong> {{ $contact->mobile }}</span><br>
                            <span><strong>Email : </strong> {{ $contact->email }}</span><br>
                            <span>{{ $contact->address }}</span>
                        </td>
                        <td width="30%">
                            <?php if(!empty($order->shop_id)){?>
                            <strong>Shop ID : {{ $order->shop_id }}</strong> </br>
                            <?php } ?>
                            <strong>Invoice No : # {{ !empty($order->invoice_no) ? $order->invoice_no : $order->order_no }}</strong>

                        </td>
                    </tr>
                    <tr>
                        <td><strong>Shipping/Billing Info :</strong></td>
                        <td colspan="2">
                            <strong>Name : </strong> {{ $order['shipping']['name'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Phone No : </strong> {{ $order['shipping']['mobile'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Email : </strong> {{ $order['shipping']['email'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Address : </strong> {{ $order['shipping']['address'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Payment : </strong>
                            <span style="background: yellow; padding:3px;">{{ $order['payment']['payment_method'] }}</span>
                          
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
                    @foreach ($order['order_details'] as $details)
                        <tr>
                            <td>
                                <img style="width: 50px;height:30px;float: left;"
                                    src="{{ url('upload/product_images/' . $details['product']['image']) }}"> &nbsp;
                                {{ $details['product']['name'] }}
                            </td>
                            <td>
                                {{ $details->product_color ? $details->product_color->color ? $details->product_color->color->name : 'N/A' : 'N/A' }} & {{ $details->product_size ? $details->product_size->size ? $details->product_size->size->name : 'N/A' : 'N/A' }}
                            </td>
                            <td>
                                @php
                                    $sub_total = $details->quantity * (($details['product']['price'])-($details['product']['discount']));
                                @endphp
                                {{ $details->quantity }} X {{ ($details['product']['price']) -($details['product']['discount']) }} = {{ $sub_total }} Tk.
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;"><strong>Sub Total</strong></td>
                        <td><strong>{{ $order->order_total }} Tk.</strong></td>
                    </tr>
                    @if ($order->coupon_discount != null)
                        <tr>
                            <td colspan="2" style="text-align: right;">Coupon Discount</td>
                            <td>{{ $order->coupon_discount }} Tk.</td>
                        </tr>
                    @endif
                    
                    <tr>
                        <td colspan="2" style="text-align: right;">Delivery Charge</td>
                        <td>{{ $order['delivery_charge'] }} Tk.</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;"><strong>Grand Total</strong></td>
                        <td><strong>{{ round($order->order_total - $order->coupon_discount + $order['delivery_charge']) }}
                                Tk.</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
