<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- Default SEO Meta (Fallback) --}}
    <meta name="description" content="@yield('meta_description', 'U Super Shop | Best Online Shop কেনাকাটা আর আয়ের সেরা ঠিকানা! সেরা ডিলে প্রিমিয়াম শপিং করুন অথবা সেলার ও ড্রপশিপার হয়ে ইনভেস্টমেন্ট ছাড়াই ব্যবসা শুরু করুন। দ্রুত ডেলিভারি ও বিশ্বস্ততার নিশ্চয়তা। আজই যোগ দিন | আনলিমিটেড রেফার বোনাসের সেরা প্ল্যাটফর্ম। এখনই ভিজিট করুন!')" />
    <meta name="keywords" content="@yield('meta_keywords', config('app.name') . ', অনলাইন শপ, গ্রোসারি, কসমেটিক্স, হেলথ কেয়ার, বেবি প্রোডাক্ট')" />
    <meta name="author" content="@yield('meta_author', config('app.name'))" />
    <meta name="robots" content="@yield('meta_robots', 'all')" />

    {{-- Open Graph (Facebook / Messenger) --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))" />
    <meta property="og:description" content="@yield('og_description', config('app.name') . ' - মানসম্মত পণ্য দ্রুত ডেলিভারি সহ')" />
    <meta property="og:image" content="@yield('og_image', asset('frontend/images/og-default.jpg'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:type" content="@yield('og_type', 'website')" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))" />
    <meta name="twitter:description" content="@yield('twitter_description', config('app.name') . ' - আপনার নির্ভরযোগ্য অনলাইন শপ')" />
    <meta name="twitter:image" content="@yield('twitter_image', asset('frontend/images/og-default.jpg'))" />

    {{-- Allow extra per-page meta tags --}}
    @stack('meta')

    <title>@yield('title', config('app.name'))</title>

    @stack('meta')

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap.min.css" />
    <!-- Customizable CSS -->
    <link rel="icon" type="image/x-icon" href="{{ asset('frontend') }}/images/favicon.ico">
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/main.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/blue.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/owl-carousel/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/owl-carousel/css/owl.theme.default.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/rateit.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css"
        type="text/css"media="all" />

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend') }}/assets/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/lightbox.css" />
    <!-- Icons/Glyphs -->
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/font-awesome.css" />
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/fonts/icofont.min.css" />
    <!-- Fonts -->
    <link href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,600italic,700,700italic,800"rel="stylesheet"
        type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
    @php
        use App\Models\ColorSetting;
        $colors = ColorSetting::getAllColors();

    @endphp
    <style>
        :root {
            --header-bg: {{ $colors['header_bg'] ?? '#0824AC' }};
            --sub-header-bg: {{ $colors['sub_header_bg'] ?? '#0a00a1' }};
            --header-text: {{ $colors['header_text'] ?? '#000000' }};
            --footer-bg: {{ $colors['footer_bg'] ?? '#202020' }};
            --footer-text: {{ $colors['footer_text'] ?? '#ffffff' }};
            --search-icon-bg: {{ $colors['search_icon_bg'] ?? '#007bff' }};
            --search-icon-color: {{ $colors['search_icon_color'] ?? '#ffffff' }};
            --add-to-cart-bg: {{ $colors['add_to_cart_bg'] ?? '#0824ac' }};
            --add-to-cart-text: {{ $colors['add_to_cart_text'] ?? '#ffffff' }};
            --price-color: {{ $colors['price_color'] ?? '#0824AC' }};
            --primary-button: {{ $colors['primary_button'] ?? '#007bff' }};
            --secondary-button: {{ $colors['secondary_button'] ?? '#6c757d' }};
        }
    </style>


    @include('frontend.layouts.css')
    @yield('custom_css')
</head>

<body class="cnt-home">
    <!-- ============ HEADER ============= -->
    @include('frontend.layouts.header')
    <!-- ============ HEADER : END ============ -->

    @yield('content')


    <!-- ============ FOOTER ============ -->
    @include('frontend.layouts.footer')

    {{-- =============== product view  modal ============= --}}
    <!-- Modal -->
    {{-- <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="pname"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding-bottom: 30px;">
                    <form method="POST" id="CardFormData">
                        <div class="row">
                            <div class="col-sm-5">
                                <button class="modal-product-image-btn" style="margin-bottom: 10px;">
                                    <img src="" class="card-img-top" id="pimage" alt=""
                                        style="height: 200px;width:160px;"
                                        onerror="this.src='{{ asset('/frontend/no-image-icon.jpg') }}'">
                                </button>
                            </div>
                            <div class="col-sm-7">
                               
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Product Code : </th>
                                            <th><span id="pcode"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Category : </th>
                                            <th><span id="pcategory"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Brand : </th>
                                            <th><span id="pbrand"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Stock : </th>
                                            <th>
                                                <span class="badge badge-pill badge-success" id="aviable" style="background:green; color:white;"></span>
                                                <span class="badge badge-pill badge-danger" id="stockout" style="background:red; color:white;"></span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Price : </th>
                                            <td id="pprice"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="color">Select Color</label>
                                            <select class="form-control" id="color" name="color">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <div class="form-group" id="sizeArea">
                                            <label for="size">Select Size</label>
                                            <select class="form-control" id="size" name="size">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: flex ; margin: auto; vertical-align: middle;">
                                    <label for="qty" style="line-height: 28px; text-transform: uppercase; margin-right: 10px;">QTY : </label>
                                    <div class=" clearfix arrows-modal">
                                        <button type="button" class="pull-left arrow minus gradient" style="padding: 7px 12px; border: 1px solid #ddd; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                            <span class="ir"><i class="fa fa-minus"></i></span>
                                        </button>
                                        <input type="text" readonly class="form-control text-center pull-left"
                                            id="qty" value="1" min="1" style="width: 50px; border-radius: 0px; border: 1px solid #ddd; font-weight: 600; font-size: 16px;">
                                        <button type="button" class="pull-left arrow plus gradient" style="padding: 7px 12px; border: 1px solid #ddd; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">
                                            <span class="ir"><i class="fa fa-plus"></i></span>
                                        </button>
                                    </div>
                                    <input type="hidden" id="product_id">
                                    <div class="addcartBtnSection" style="margin: auto; margin-left: 0px;">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="pname"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding-bottom: 30px;">
                    <div class="add_to_cart_product_data">
                        <div class="row">
                            <div class="col-sm-5">
                                <button class="modal-product-image-btn" style="margin-bottom: 10px;">
                                    <img src="" class="card-img-top" id="pimage" alt=""
                                        style="height: 200px;width:160px;"
                                        onerror="this.src='{{ asset('/frontend/no-image-icon.jpg') }}'">
                                </button>
                            </div>
                            <div class="col-sm-7">

                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Product Code : </th>
                                            <th><span id="pcode"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Category : </th>
                                            <th><span id="pcategory"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Brand : </th>
                                            <th><span id="pbrand"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Stock : </th>
                                            <th>
                                                <span class="badge badge-pill badge-success" id="aviable"
                                                    style="background:green; color:white;"></span>
                                                <span class="badge badge-pill badge-danger" id="stockout"
                                                    style="background:red; color:white;"></span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Price : </th>
                                            <td id="pprice"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            <label for="color">Select Color</label>
                                            <select class="form-control" id="color" name="color">
                                            </select>
                                            <span class="text-danger color_id_errors"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <div class="form-group" id="sizeArea">
                                            <label for="size">Select Size</label>
                                            <select class="form-control" id="size" name="size">
                                            </select>
                                            <span class="text-danger size_id_errors"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: flex ; margin: auto; vertical-align: middle;">
                                    <label for="qty"
                                        style="line-height: 28px; text-transform: uppercase; margin-right: 10px;">QTY :
                                    </label>
                                    <div class=" clearfix arrows-modal">
                                        <button type="button" class="pull-left arrow minus gradient"
                                            style="padding: 7px 12px; border: 1px solid #ddd; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                                            <span class="ir"><i class="fa fa-minus"></i></span>
                                        </button>
                                        <input type="text" readonly class="form-control text-center pull-left"
                                            id="qty" value="1" min="1"
                                            style="width: 50px; border-radius: 0px; border: 1px solid #ddd; font-weight: 600; font-size: 16px;">
                                        <button type="button" class="pull-left arrow plus gradient"
                                            style="padding: 7px 12px; border: 1px solid #ddd; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">
                                            <span class="ir"><i class="fa fa-plus"></i></span>
                                        </button>
                                    </div>
                                    <input type="hidden" id="product_id">

                                    @if (auth()->check() && auth()->user()->usertype == 'dropshipper')
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <input type="hidden" id="product-dropshipper-price" class="form-control"
                                                value="0">
                                            <label>Your Selling Price</label>
                                            <input type="number" oninput="showProfitt()" name="selling_price"
                                                step="0.01" id="selling_price" value="{{ old('selling_price') }}"
                                                class="form-control @error('selling_price') is-invalid @enderror"
                                                placeholder="Enter your selling price" required>
                                            <small class="text-muted dropshipper-range" id="dropshipper-range">
                                                Allowed range:

                                            </small><br>
                                            <small id="profit_display" class="text-muted text-danger">

                                            </small>
                                            @error('selling_price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif


                                    <div class="addcartBtnSection" style="margin: auto; margin-left: 0px;">

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============= FOOTER : END ============ -->
    <script src="{{ asset('frontend') }}/assets/js/bootstrap.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/owl-carousel/js/owl.carousel.min.js"></script>

    <script src="{{ asset('frontend') }}/assets/js/echo.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/jquery.easing-1.3.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/bootstrap-slider.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/jquery.rateit.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/lightbox.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/wow.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/scripts.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/select2.min.js"></script>
    <script>
        $(".js-select2").each(function() {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        });
        $(document).ready(function() {
            $(".plus").click(function() {
                let qty = $("#qty");
                let currentVal = parseInt(qty.val());
                if (!isNaN(currentVal)) {
                    qty.val(currentVal + 1);
                }
            });

            $(".minus").click(function() {
                let qty = $("#qty");
                let currentVal = parseInt(qty.val());
                if (!isNaN(currentVal) && currentVal > 1) {
                    qty.val(currentVal - 1);
                }
            });
        });
    </script>

    <script src="{{ asset('frontend') }}/assets/js/sweetalert.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js">
    </script>
    <script src="{{ asset('backend') }}/plugins/jquery-validation/jquery.validate.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.2.4/dist/flasher.min.js"></script>
    @if (session()->has('success'))
        {{ session()->get('success') }}
    @endif
    @if (session()->has('error'))
        {{ session()->get('error') }}
    @endif
    @if (session()->has('warning'))
        {{ session()->get('warning') }}
    @endif
    @if (session()->has('info'))
        {{ session()->get('info') }}
    @endif


    <script>
        function error_msg(message) {
            flasher['error'](message, {
                timeout: 1500
            });
        }

        function warning_msg(message) {
            flasher['warning'](message, {
                timeout: 1500
            });
        }

        function success_msg(message) {
            flasher['success'](message, {
                timeout: 1500
            });
        }

        function info_msg(message) {
            flasher['info'](message, {
                timeout: 1500
            });
        }
    </script>


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        //start product view with modal
        function productView(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('/product/view/modal/') }}/" + id,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    let discount = data.product.discount;
                    let product_price = data.product.price;
                    let discount_price = product_price - discount;
                    //console.log(discount_price);
                    $('#pname').text(data.product.name);
                    $('#price').text(data.product.price);
                    $('#price').text(data.product.price);

                    $('#product-dropshipper-price').val(data.product.price);
                    $('#dropshipper-range').text("Allow Range: " + data.product.min_price + " - " + data.product
                        .max_price);
                    $('#pcode').text(data.product.id);
                    $('#pcategory').text(data.product.category.name);
                    $('#pbrand').text(data.product.brand.name);
                    $('#pimage').attr('src', '{{ asset('/upload/product_images') }}/' + data.product.image);
                    $('#product_id').val(id);
                    $('#qty').val(1);
                    //product price
                    if (data.product.discount == null) {
                        $('#pprice').html('<span>&#2547; ' + data.product.price + '</span>');
                    } else {
                        $('#pprice').html('<strong><span>&#2547;' + discount_price +
                            '</span> <del style="color: #989191;">&#2547;' + data.product.price +
                            '</del></strong>');
                    }

                    //stock
                    if (data.product.quantity > 0) {
                        $('#aviable').text('');
                        $('#stockout').text('');
                        $('#aviable').text('Available');

                        $('.addcartBtnSection').html(
                            `<button type="button" class="btn btn-success cartBtn" onclick="addToCart(this, ${data.product.id});" style="margin-left: 12px;">Add To Cart</button>`
                        );
                    } else {
                        $('#aviable').text('');
                        $('#stockout').text('');
                        $('#stockout').text('Stock Out');
                        $('.addcartBtnSection').html(
                            `<span style="border: 1px solid #ff00009c; padding: 8px 15px; font-weight: 600; border-radius: 4px; margin: 15px; background: #ff00000a; color: #ff0000;">Stock Out</span>`
                        );
                    }

                    //color
                    $('select[name="color"]').empty();
                    var select_color_items = `<option value="">Select Color</option>`;
                    $.each(data.color, function(key, value) {
                        select_color_items += '<option value="' + value.id + '">' + value.color.name +
                            '</option>';
                    })
                    $('select[name="color"]').html(select_color_items);

                    //size
                    $('select[name="size"]').empty();
                    var select_size_items = `<option value="">Select Size</option>`;
                    $.each(data.size, function(key, value) {
                        select_size_items += '<option value="' + value.id + '">' + value.size.name +
                            '</option>';

                        if (data.size == "") {
                            $('#sizeArea').hide();
                        } else {
                            $('#sizeArea').show();
                        }
                    })

                    $('select[name="size"]').html(select_size_items);
                }
            })
        }
        //end product view with modal
        // $('#CardFormData').on('submit', function(event) {
        //     event.preventDefault();
        //     var crproduct_name = $('#pname').text();
        //     var id = $('#product_id').val();
        //     var crcolor = $('#color option:selected').val();
        //     var crsize = $('#size option:selected').val();
        //     var crquantity = $('#qty').val();
        //     $.ajax({
        //         url: "{{ url('/cart/data/store/') }}/" + id,
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             color: crcolor,
        //             size: crsize,
        //             quantity: crquantity,
        //             product_name: crproduct_name
        //         },
        //         success: function(data) {
        //             console.log(data);
        //             $('#closeModal').click();
        //             if (data.success) {
        //                 $.notify(data.success, "success");
        //                 // Redirect to shopping cart page after a delay
        //                 setTimeout(function() {
        //                     location.reload();
        //                 }, 2000);
        //             } else {
        //                 location.reload();
        //                 $.notify(data.error, "error");
        //             }
        //         }
        //     })
        // });

        // //Start add to cart product
        // function addToCart() {
        //     var product_name = $('#pname').text();
        //     var id = $('#product_id').val();
        //     var color = $('#color option:selected').val();
        //     var size = $('#size option:selected').val();
        //     var quantity = $('#qty').val();

        //     $.ajax({
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             color: color,
        //             size: size,
        //             quantity: quantity,
        //             product_name: product_name
        //         },
        //         url: "{{ url('/cart/data/store/') }}/" + id,
        //         success: function(data) {

        //             $('#closeModal').click();
        //             //  start message
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: 'top-end',
        //                 showConfirmButton: false,
        //                 timer: 3000
        //             })

        //             if ($.isEmptyObject(data.error)) {
        //                 Toast.fire({
        //                     type: 'success',
        //                     title: data.success
        //                 })
        //             } else {
        //                 Toast.fire({
        //                     type: 'error',
        //                     title: data.error
        //                 })
        //             }
        //             //  end message
        //         }
        //     })
        // }
        // //End add to cart product



        function addToCart(cartBtn, product_id) {
            var closest = $(cartBtn).closest('.add_to_cart_product_data');

            closest.find('.color_id_errors').text('');
            closest.find('.size_id_errors').text('');

            var quantity = closest.find('#qty').val() || 1;
            var color_id = closest.find('#color').val() || '';
            var size_id = closest.find('#size').val() || '';
            var drop_selling_price = $('#selling_price').val() || '';

            var url = "{{ route('cart.customer.customerCartStore') }}";

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    product_id: product_id,
                    qty: quantity,
                    color_id: color_id,
                    size_id: size_id,
                    drop_selling_price: drop_selling_price,
                    _token: "{{ csrf_token() }}"
                },
                url: url,
                success: function(res) {

                    if (res.status == true) {
                        if (res.type == 'success') {
                            success_msg('' + res.message);
                            getAddToCartData();
                        } else if (res.type == 'increase') {
                            success_msg('' + res.message);
                            getAddToCartData();
                        } else {
                            error_msg('' + res.message);
                        }
                    } else {
                        if (res.errors && Object.keys(res.errors).length > 0) {
                            Object.entries(res.errors).forEach(([field, messages]) => {
                                // Display field-specific error messages
                                if (field === 'color_id') {
                                    closest.find('.color_id_errors').text(messages[0]);
                                }
                                if (field === 'size_id') {
                                    closest.find('.size_id_errors').text(messages[0]);
                                }

                                // Optionally show all messages as general error notifications
                                messages.forEach(message => {
                                    error_msg(message);
                                });
                            });
                        } else {
                            error_msg('' + res.message);
                        }
                    }
                },
                error: function() {
                    console.log('something went wrong!');
                }
            })
        }

        $(document).ready(function() {
            getAddToCartData();
        })

        function getDiscountPrice(price, discount, discountType) {

            var finalPrice;

            if (!isNaN(discount) && discount > 0) {
                if (discountType == 1) {
                    finalPrice = price - (price * discount) / 100;
                } else {
                    finalPrice = price - discount;
                }
            } else {
                finalPrice = price;
            }

            return finalPrice;
        }

        function getAddToCartData() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('cart.customer.customerCartData') }}",

                success: function(res) {
                    let subtotal = 0;
                    let html = "";

                    if (Array.isArray(res) && res.length > 0) {

                        res.forEach(item => {
                            const product = item.product;

                            // Safe price conversion
                            const price = Number(product.price) || 0;
                            const qty = Number(item.qty) || 1;

                            // subtotal calculation (price × quantity)
                            const totalForItem = price * qty;
                            subtotal += totalForItem;

                            html += `
                        <div class="item">
                            <div class="image">
                                <a href="#">
                                    <button><img src="${product.image}" alt="" /></button>
                                </a>
                            </div>

                            <div class="details">
                                <h3 class="name">
                                    ${product.name.length > 20 ? product.name.substring(0, 20) + "..." : product.name}
                                </h3>
                                <p class="price">${qty} x ${price} Tk.</p>
                            </div>

                            <span class="action" onclick="destroyCartItem(${item.id})">
                                <i class="fa fa-close"></i>
                            </span>
                        </div>
                    `;
                        });
                    }

                    // Insert HTML
                    $(".addToCartProducts").html(html);

                    // Subtotal update
                    $(".addTotalCartItemSection .cart-total .price").text(subtotal.toFixed(2));

                    // Total item count
                    $(".items-cart-inner .basket-item-count .count").text(res.length);
                },

                error: function() {
                    console.log("Something went wrong!");
                }
            });
        }


        function destroyCartItem(cart_id) {
            if (cart_id) {
                var url = "{{ route('cart.customer.customerCartDestroy', ['cart_id' => ':cart_id']) }}";
                url = url.replace(':cart_id', cart_id);

                $.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: url,
                    success: function(res) {
                        if (res.status == true) {
                            success_msg('' + res.message);
                            getAddToCartData();
                        } else {
                            error_msg('' + res.message);
                        }
                    },
                    error: function() {
                        console.log('Something went wrong!');
                    }
                });
            }
        }


        function addToWishlist(product_id) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "{{ url('/add_to_wishlist/') }}/" + product_id,
                success: function(data) {
                    //  start message
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    })

                    if ($.isEmptyObject(data.error)) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: data.error
                        })
                    }
                    //  end message
                }
            })
        }


        function categoryMobile() {
            $(".mobile-header .nav-sidebar").toggleClass("active");
        }

        $("#owl-main").owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            nav: false,
            dots: false,
        });

        $('.new_productsTab').each(function() {
            var owlhotone = $(this);
            owlhotone.owlCarousel({
                loop: true,
                autoplay: true,
                items: 2,
                dots: false,
                navigation: false,
                pagination: false,
                navigationText: ["", ""],
                responsive: {
                    0: {
                        items: 2
                    },
                    350: {
                        items: 2
                    },
                    380: {
                        items: 2
                    },
                    450: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    556: {
                        items: 3
                    },
                    650: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    1000: {
                        items: 6
                    }
                }
            });
        });

        $('.special_offer_carousel').each(function() {
            var offerone = $(this);
            offerone.owlCarousel({
                loop: true,
                autoplay: true,
                items: 2,
                dots: false,
                navigation: false,
                pagination: false,
                navigationText: ["", ""],
                responsive: {
                    0: {
                        items: 2
                    },
                    350: {
                        items: 2
                    },
                    380: {
                        items: 2
                    },
                    450: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    556: {
                        items: 3
                    },
                    650: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    1000: {
                        items: 6
                    }
                }
            });
        });

        $('.special_deals_err').each(function() {
            var deals = $(this);
            deals.owlCarousel({
                loop: true,
                autoplay: true,
                items: 2,
                dots: false,
                navigation: false,
                pagination: false,
                navigationText: ["", ""],
                responsive: {
                    0: {
                        items: 2
                    },
                    350: {
                        items: 2
                    },
                    380: {
                        items: 2
                    },
                    450: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    556: {
                        items: 3
                    },
                    650: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    1000: {
                        items: 6
                    }
                }
            });
        });

        $('.featuredProducts').each(function() {
            var deals = $(this);
            deals.owlCarousel({
                loop: true,
                autoplay: true,
                items: 2,
                dots: false,
                navigation: false,
                pagination: false,
                navigationText: ["", ""],
                responsive: {
                    0: {
                        items: 2
                    },
                    350: {
                        items: 2
                    },
                    380: {
                        items: 2
                    },
                    450: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    556: {
                        items: 3
                    },
                    650: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    1000: {
                        items: 6
                    }
                }
            });
        });

        $('.categoryProducts').each(function() {
            var catProducts = $(this);
            catProducts.owlCarousel({
                loop: true,
                autoplay: true,
                items: 2,
                dots: false,
                navigation: false,
                pagination: false,
                navigationText: ["", ""],
                responsive: {
                    0: {
                        items: 2
                    },
                    350: {
                        items: 2
                    },
                    380: {
                        items: 2
                    },
                    450: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    556: {
                        items: 3
                    },
                    650: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    1000: {
                        items: 6
                    }
                }
            });
        });

        $(document).ready(function() {
            function updatePriceAndStock() {
                let product_id = $('#product_id').val(); // hidden input with product ID
                let color_id = $('#color').val();
                let size_id = $('#size').val();

                if (!product_id) return;

                $.ajax({
                    url: "{{ route('get.product.price') }}",
                    type: "GET",
                    data: {
                        product_id: product_id,
                        color_id: color_id,
                        size_id: size_id
                    },
                    success: function(response) {
                        // Response contains: price, original_price, discount
                        let price = response.price;
                        let original_price = response.original_price;
                        let discount = response.discount;

                        if (discount && discount > 0) {
                            $('#pprice').html(
                                `<strong>&#2547; ${price}</strong> 
                        <del style="color: #989191;">&#2547; ${original_price}</del>`
                            );
                        } else {
                            $('#pprice').html(`<span>&#2547; ${price}</span>`);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        $('#pprice').html(`<span style="color:red;">Error!</span>`);
                    }
                });
            }

            // Trigger update on color/size change
            $('#color, #size').on('change', updatePriceAndStock);


        });

        function showProfitt() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const defaultPrice = parseFloat(document.getElementById('product-dropshipper-price').value) || 0;
            const profit = sellingPrice - defaultPrice;
            if (!isNaN(profit)) {
                $("#profit_display").text('Profit: ৳' + profit.toFixed(2));
            }
        }

        $(document).ready(function() {
            // SEO Fix: Ensure all links are crawlable (especially Lightbox close button)
            const fixLinks = () => {
                $('a.lb-close, a.lb-prev, a.lb-next, a.nav-close').each(function() {
                    if (!$(this).attr('href')) {
                        $(this).attr('href', 'javascript:void(0)');
                    }
                });
            };
            
            // Run on load and on any click that might open a lightbox
            fixLinks();
            $(document).on('click', '[data-lightbox], .header-user', function() {
                setTimeout(fixLinks, 500);
            });
        });
    </script>




    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/67769592af5bfec1dbe5cfa4/1j8nukq3o';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
    @stack('custom_script')
</body>

</html>
