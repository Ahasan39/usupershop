@extends('frontend.layouts.master')
@section('title')
    Order Tracking
@endsection
@section('custom_css')
    <style>
        @import url('https://fonts.googleapis.com/css?family=Open+Sans&display=swap');

        .card {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.10rem
        }

        .card-header:first-child {
            border-radius: calc(0.37rem - 1px) calc(0.37rem - 1px) 0 0
        }

        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1)
        }

        .track {
            position: relative;
            background-color: #ddd;
            height: 7px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            margin-bottom: 60px;
            margin-top: 50px
        }

        .track .step {
            -webkit-box-flex: 1;
            -ms-flex-positive: 1;
            flex-grow: 1;
            width: 25%;
            margin-top: -18px;
            text-align: center;
            position: relative
        }

        .track .step.active:before {
            background: #59b210
        }

        .track .step::before {
            height: 7px;
            position: absolute;
            content: "";
            width: 100%;
            left: 0;
            top: 18px
        }

        .track .step.active .icon {
            background: #59b210;
            color: #fff
        }

        /* cancel area start */
        .track .cancel {
            -webkit-box-flex: 1;
            -ms-flex-positive: 1;
            flex-grow: 1;
            width: 25%;
            margin-top: -18px;
            text-align: center;
            position: relative
        }

        .track .cancel.done:before {
            background: #EA323D;
        }

        .track .cancel::before {
            height: 7px;
            position: absolute;
            content: "";
            width: 100%;
            left: 0;
            top: 18px
        }

        .track .cancel.done .icon {
            background: #EA323D;
            color: #fff
        }

        .track .icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            position: relative;
            border-radius: 100%;
            background: #ddd;
        }

        .track .cancel.done .text {
            font-weight: 400;
            color: #000
        }

        /* end cancel area  */

        .track .icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            position: relative;
            border-radius: 100%;
            background: #ddd;
        }

        .track .step.active .text {
            font-weight: 400;
            color: #000
        }

        .track .text {
            display: block;
            margin-top: 7px
        }

        .itemside {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            width: 100%
        }

        .itemside .aside {
            position: relative;
            -ms-flex-negative: 0;
            flex-shrink: 0
        }

        .img-sm {
            width: 80px;
            height: 80px;
            padding: 7px
        }

        ul.row,
        ul.row-sm {
            list-style: none;
            padding: 0
        }

        .itemside .info {
            padding-left: 15px;
            padding-right: 7px
        }

        .itemside .title {
            display: block;
            margin-bottom: 5px;
            color: #212529
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem
        }

        .btn-warning {
            color: #ffffff;
            background-color: #59b210;
            border-color: #59b210;
            border-radius: 1px
        }

        .btn-warning:hover {
            color: #ffffff;
            background-color: #59b210;
            border-color: #59b210;
            border-radius: 1px
        }

        .qty {
            background: #59b210;
        }

        .mt {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .empty {
            padding: 20px;
        }

        /* Base styles for all screen sizes */
        .container,
        .row,
        .card,
        .card-body,
        .track,
        ul.row,
        li.col {
            width: 100%;
            box-sizing: border-box;
        }

        /* Ensure images scale appropriately */
        .itemside img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Flexbox for the tracking steps */
        .track {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
        }

        /* Individual step styling */
        .track .step {
            flex: 1 1 100%;
            text-align: center;

        }

        /* Responsive adjustments for larger screens */
        @media (min-width: 768px) {
            .track .step {
                flex: 1 1 calc(20% - 1rem);
            }

        }
        .body-content{
            min-height: 80vh;
            padding: 10px 0px;
        }

        /* Responsive for mobile */
        @media screen and (max-width: 768px) {

            .body-content{
                margin-top: 110px;
            }

            .track {
                flex-direction: column;
                width: 88%;
                align-items: center;
            }

            .step {
                width: 88%;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
            }

            .step .icon {
                margin-right: 8px;
                margin-bottom: 0;
            }

            .step .text {
                font-size: 14px;
            }

            .step small {
                font-size: 11px;
            }

            .step::after {
                display: none;
            }

            .track .step {
                width: 20%;
                margin-top: -18px;
                text-align: center;
                position: relative
            }
        }
    </style>
@endsection
@section('content')

    <div class="body-content">
        <div class="container">
            <div class="row" style="margin-left: auto; margin-right: auto;">
                <div class="col-12 my-wishlist-page">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Invoice No:</strong> <br> {{ $order->invoice_no }}
                                </div>
                                <div class="col-md-2"> <strong>Order Date:</strong> <br>{{ $order->created_at }}
                                </div>
                                <div class="col-md-5"> <strong>Shipping By: {{ $order->shipping->name }}</strong>
                                    <br>
                                    {{ $order->shipping->address }},
                                    | <i class="fa fa-phone"></i> {{ $order->shipping->mobile }}
                                </div>
                                <div class="col-md-2"> <strong>Status:</strong> <br> Picked by the courier </div>
                                <div class="col-md-1"> <strong>Total: </strong> <br>৳ {{ $order->order_total }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="track">

                                @if ($order->status === 'pending')
                                    <div class="step active">
                                        <span class="icon"> <i class="fa fa-spinner"></i> </span>
                                        <span class="text">Order Pending</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</small>
                                    </div>
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-shopping-cart"></i> </span>
                                        <span class="text">Order Confirmed</span>
                                    </div>
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-truck"></i> </span>
                                        <span class="text">Packaging Order</span>
                                    </div>
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-user"></i> </span>
                                        <span class="text">Delivered Order</span>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-hand-lizard-o"></i> </span>
                                        <span class="text">Return Order</span>
                                    </div>
                                @elseif ($order->status === 'confirmed')
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-spinner"></i> </span>
                                        <span class="text">Order Pending</span>
                                        <small class="text-danger">
                                            {{ Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</small>
                                    </div>
                                    <div class="step active">
                                        <span class="icon"> <i class="fa fa-shopping-cart"></i> </span>
                                        <span class="text">Order Confirmed</span>
                                        <small class="text-danger">
                                            {{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}
                                        </small>
                                    </div>
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-truck"></i> </span>
                                        <span class="text">Packaging Order</span>
                                    </div>
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-user"></i> </span>
                                        <span class="text">Delivered Order</span>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-hand-lizard-o"></i> </span>
                                        <span class="text">Return Order</span>
                                    </div>
                                @elseif ($order->status === 'packaging')
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-spinner"></i> </span>
                                        <span class="text">Order Pending</span>
                                        <small class="text-danger">{{ $order->updated_at }}</small>
                                    </div>
                                    <div class="step active">
                                        <span class="icon "><i class="fa fa-check"></i> </span>
                                        <span class="text">Order packaging</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}</small>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-user"></i> </span>
                                        <span class="text">Delivered Order</span>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-hand-lizard-o"></i> </span>
                                        <span class="text">Return Order</span>
                                    </div>
                                @elseif ($order->status === 'canceled')
                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-spinner"></i> </span>
                                        <span class="text">Order Pending</span>
                                        <small class="text-danger">{{ $order->updated_at }}</small>
                                    </div>
                                    <div class="step active">
                                        <span class="icon "><i class="fa fa-check"></i> </span>
                                        <span class="text">Order canceled</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}</small>
                                    </div>
                                    <div class="step">
                                        <span class="icon "><i class="fa fa-check"></i> </span>
                                        <span class="text">Order packaging</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}</small>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-user"></i> </span>
                                        <span class="text">Delivered Order</span>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-hand-lizard-o"></i> </span>
                                        <span class="text">Return Order</span>
                                    </div>
                                @elseif ($order->status === 'delivered')
                                    <div class="step ">
                                        <span class="icon"> <i class="fa fa-spinner"></i> </span>
                                        <span class="text">Order Pending</span>
                                        <small class="text-danger">{{ $order->updated_at }}</small>
                                    </div>
                                    <div class="step ">
                                        <span class="icon "><i class="fa fa-check"></i> </span>
                                        <span class="text">Order confirmed</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}</small>
                                    </div>
                                    <div class="step ">
                                        <span class="icon "><i class="fa fa-check"></i> </span>
                                        <span class="text">Order packaging</span>

                                    </div>

                                    <div class="step active">
                                        <span class="icon"> <i class="fa fa-user"></i> </span>
                                        <span class="text">Delivered Order</span>
                                        <small
                                            class="text-danger">{{ Carbon\Carbon::parse($order->updated_at)->format('d F Y') }}</small>
                                    </div>

                                    <div class="step">
                                        <span class="icon"> <i class="fa fa-hand-lizard-o"></i> </span>
                                        <span class="text">Return Order</span>
                                    </div>
                                @else
                                    <div class="cancel done">
                                        <span class="icon "> <i class="fa fa-close "></i> </span>
                                        <span class="text">Order Pending</span>
                                    </div>
                                    <div class="cancel done">
                                        <span class="icon "><i class="fa fa-close"></i> </span>
                                        <span class="text">Order confirmed</span>
                                    </div>
                                    <div class="cancel done">
                                        <span class="icon"> <i class="fa fa-close"></i> </span>
                                        <span class="text">Order Processing</span>
                                    </div>
                                    <div class="cancel done">
                                        <span class="icon"> <i class="fa fa-close"></i> </span>
                                        <span class="text">Picked Order</span>
                                    </div>

                                    <div class="cancel done">
                                        <span class="icon"><i class="fa fa-close"></i> </span>
                                        <span class="text">Shipped Order</span>
                                    </div>
                                    <div class="cancel done">
                                        <span class="icon"> <i class="fa fa-close"></i></span>
                                        <span class="text">Delivered</span>
                                    </div>
                                @endif
                            </div>
                            <div class="empty"></div>

                            <hr>
                            <ul class="row">
                                <div class="col-md-12">

                                    @foreach ($orderItems as $item)
                                        <li class="col col-md-3 col-sm-6  mt-3">
                                            <figure class="itemside mb-3">
                                                <div class="aside"style="height:80px; width:80px;">
                                                    @if (!empty($item->product->image))
                                                        <img src="{{ url('upload/product_images/' . $item->product->image) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('frontend/no-image-icon.jpg') }}';"
                                                            alt="Product Image" />
                                                    @else
                                                        <img src="{{ asset('frontend/no-image-icon.jpg') }}"
                                                            alt="Product Image" />
                                                    @endif



                                                </div>
                                                @if ($item->product)
                                                    <figcaption class="info align-self-center" style="width:60%;">
                                                        <p class="title">
                                                            {{ $item->product->name }}
                                                            <br>{{ $item->size_name }} <br> {{ $item->color_name }}
                                                        </p>
                                                        <span class="text-muted">
                                                            <span
                                                                class="badge badge-pill badge-primary qty">{{ $item->qty }}
                                                                pieces</span>
                                                        </span>
                                                    </figcaption>
                                                @else
                                                    <figcaption class="info align-self-center" style="width:60%;">
                                                        <p class="title text-danger">Product not available</p>
                                                    </figcaption>
                                                @endif

                                            </figure>
                                        </li>
                                    @endforeach
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
