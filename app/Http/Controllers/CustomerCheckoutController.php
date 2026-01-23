<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use App\Models\Shipping;
use App\Traits\BkashPaymentTrait;
use App\Traits\EPSGatewayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class CustomerCheckoutController extends Controller
{
    use BkashPaymentTrait, EPSGatewayTrait;

    private function generateOrderNumber()
    {
        $time = time();
        $lastOrder = Order::orderBy('id', 'DESC')->first();
        $order_no = $lastOrder ? $lastOrder->id + 1 : 1;
        return $time . $order_no;
    }

    public function CustomerOrderCheckout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'type' => 'auth',
                'message' => 'User login required'
            ]);
        }

        if ($user->usertype !== 'customer' && $user->usertype !== 'dropshipper') {
            return response()->json([
                'status' => false,
                'type' => 'user_type',
                'message' => $user->usertype . ' cannot place an order!'
            ]);
        }

        // Validate request
        $request->validate([
            'name' => ['required'],
            'email' => ['nullable', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'mobile' => ['required', 'regex:/(^(\+8801|8801|01|008801))[1|5-9]{1}(\d){8}$/'],
            'address' => ['required'],
            'delivery_area' => ['required'],
            'payment_method' => ['required'],
        ]);

        // Get delivery charge
        $deliveryArea = DeliveryZone::find($request->delivery_area);
        if (!$deliveryArea) {
            return response()->json([
                'status' => false,
                'type' => 'delivery_area',
                'message' => 'Delivery Area Not Found!'
            ]);
        }
        $delivery_charge = $deliveryArea->zone_charge ?? 0;

        if (!$request->hasCookie('customer_cookie_id')) {
            return response()->json([
                'status' => false,
                'type' => 'cart',
                'message' => 'Cart data not found!'
            ]);
        }

        $cookie_id = $request->cookie('customer_cookie_id');
        $cart_data = Cart::with(['product', 'product_color.color', 'product_size.size', 'product.variants'])->where('cookie_id', $cookie_id)->get();

        if ($cart_data->isEmpty()) {
            return response()->json([
                'status' => false,
                'type' => 'cart',
                'message' => 'Cart is empty!'
            ]);
        }

        $order_total_amount = 0;
        $dropshipper_profit_total = 0;
        $orderItemsData = [];

        foreach ($cart_data as $cart) {
            $product = $cart->product;

            if (!$product)
                continue;

            // Determine variant price
            $variantPrice = $product->price; // base price

            if ($cart->color_id || $cart->size_id) {
                $prductColor = ProductColor::find($cart->color_id);
                $productSize = ProductSize::find($cart->size_id);
                $variant = ProductVariant::where('product_id', $product->id)
                    ->when($cart->color_id, fn($q) => $q->where('color_id', $prductColor?->color_id))
                    ->when($cart->size_id, fn($q) => $q->where('size_id', $productSize?->size_id))
                    ->first();

                if ($variant && $variant->additional_price) {
                    $variantPrice += $variant->additional_price;
                }
            }

            // Apply discount
            if (!empty($product->discount)) {
                if ($product->discount_type == 1) {
                    $variantPrice -= ($variantPrice * $product->discount / 100);
                } else {
                    $variantPrice -= $product->discount;
                }
            }

            // Check stock
            if ($product->quantity < $cart->qty) {
                return response()->json([
                    'status' => false,
                    'type' => 'stock',
                    'message' => "Product '{$product->name}' is out of stock or insufficient quantity."
                ]);
            }

            $line_total = $variantPrice * $cart->qty;
            $dropshipper_profit = 0;

            if (auth()->check() && $user->usertype == 'dropshipper' && $cart->drop_selling_price > 0) {

                // cost price (default)
                $cost_price = $variantPrice;

                // selling price (dropshipper)
                $selling_price = $cart->drop_selling_price;

                // calculate profit
                $dropshipper_profit = ($selling_price - $cost_price) * $cart->qty;

                // update variant price to selling price
                $variantPrice = $selling_price;

                // now calculate line total using updated price
                $line_total = $variantPrice * $cart->qty;
            }


            $order_total_amount += $line_total;
            $dropshipper_profit_total += $dropshipper_profit;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'color_id' => $cart->color_id ?? 0,
                'color_name' => $cart->product_color ? $cart->product_color->color->name : null,
                'size_id' => $cart->size_id ?? 0,
                'size_name' => $cart->product_size ? $cart->product_size->size->name : null,
                'quantity' => $cart->qty,
                'buy_price' => $product->trade_price ?? 0,
                'sell_price' => $variantPrice,
                'vendor_id' => $product->user_id ?? null,
                'reseller_id' => null,
                'dropshipper_sell_price' => $user->usertype == 'dropshipper' ? $variantPrice : 0,
                'dropshipper_profit' => $user->usertype == 'dropshipper' ? $dropshipper_profit : 0,
                'created_at' => now(),
            ];
        }

        $grand_total_amount = $order_total_amount + $delivery_charge;

        // Payment method mapping
        $payment_method = match ($request->payment_method) {
            'bkash' => 1,
            'eps' => 2,
            'cod' => 3,
            default => 0,
        };
        $payment_amount = ($payment_method === 3) ? $delivery_charge : $grand_total_amount;

        $order_status = $payment_method < 1 ? 'confirmed' : 'pending';
        $order_payment_status = $payment_method < 1 ? 'Paid' : 'Unpaid';

        DB::beginTransaction();
        try {
            // Shipping
            $shipping = Shipping::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
            ]);

            // Payment
            $payment = Payment::create(['payment_method' => $request->payment_method]);

            // Order
            $order = Order::create([
                'user_id' => $user->id,
                'dropshipper_id' => ($user->usertype == 'dropshipper') ? $user->id : '',
                'shipping_id' => $shipping->id,
                'payment_id' => $payment->id,
                'invoice_no' => $payment->id,
                'order_no' => $this->generateOrderNumber(),
                'delivery_charge' => $delivery_charge,
                'coupon_discount' => 0,
                'order_total' => $order_total_amount,
                'grand_total' => $grand_total_amount,
                'status' => $order_status,
                'order_payment' => $order_payment_status,
                'pay_method' => $payment_method,
                'dropshipper_profit' =>  $user->usertype == 'dropshipper' ? $dropshipper_profit_total : 0,
            ]);

            // Update invoice
            $order->update(['invoice_no' => $order->id . '_' . time()]);

            // Insert order items
            if (!empty($orderItemsData)) {
                foreach ($orderItemsData as &$item) {
                    $item['order_id'] = $order->id;
                }
                OrderDetail::insert($orderItemsData);

                // Decrement stock
                foreach ($cart_data as $cart) {
                    $product = $cart->product;
                    if ($product)
                        $product->decrement('quantity', $cart->qty);
                }
            }

            // Process payment
            $payment_url = null;

            if ($request->payment_method === 'bkash') {
                $payment_url = $this->processBkashPayment($payment_amount, $order->id);
            } elseif ($request->payment_method === 'eps') {
                $payment_url = $this->processEPSpaymenyGateway($payment_amount, $order->id);
            } elseif ($payment_method === 3) {
                 $payment_url = $this->processBkashPayment($payment_amount, $order->id);
            }

            DB::commit();
            Cookie::queue(Cookie::forget('customer_cookie_id'));

            if ($payment_url && is_array($payment_url) && $payment_url['status'] === true) {
                return response()->json([
                    'status' => true,
                    'type' => 'redirect_payment',
                    'url' => $payment_url['url'],
                    'message' => 'Redirecting to payment gateway.'
                ]);
            }
            if (auth()->user()->usertype == 'dropshipper') {
                return response()->json([
                    'status' => true,
                    'url' => route('dropshipper.orders.details', ['id' => $order->id]),
                    'message' => 'Order placed successfully!'
                ]);
            }
            if (auth()->user()->usertype == 'customer') {
                return response()->json([
                    'status' => true,
                    'url' => route('customer.order.details', ['id' => $order->id]),
                    'message' => 'Order placed successfully!'
                ]);
            }

        } catch (\Exception $e) {
          //  dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Something went wrong during checkout.',
                'error_msg' => $e->getMessage(),
            ]);
        }
    }



    public function getDiscountPrice($price, $discount, $discountType)
    {
        $finalPrice = $price;
        if (is_numeric($discount) && $discount > 0) {
            if ($discountType == 1) {
                $finalPrice = $price - ($price * $discount) / 100;
            } else {
                $finalPrice = $price - $discount;
            }
        }

        return $finalPrice;
    }

    private function processBkashPayment($payment_amount, $orderID)
    {
        $grantToken = $this->grantToken();
        if (isset($grantToken['id_token'])) {
            $data = [
                'amount' => $payment_amount,
                'merchantInvoiceNumber' => $orderID . '-' . time(),
                'token_id' => $grantToken['id_token'],
                'success_url' => '?payment_type=customer_order'
            ];
            $paymentCreate = $this->paymentCreate($data);
            if ($paymentCreate['status']) {
                if (isset($paymentCreate['data']['bkashURL'])) {
                    return [
                        'status' => true,
                        'url' => $paymentCreate['data']['bkashURL'],
                        'paymentID' => $paymentCreate['data']['paymentID'],
                    ];
                }
            }
        }
        return [
            'status' => false,
            'url' => '',
            'paymentID' => '',
        ];
    }

    public function processEPSpaymenyGateway($amount, $orderID)
    {
        $tokenData = $this->getToken();
        if (!$tokenData['status']) {
            return [
                'status' => false,
                'message' => 'EPS Token generation failed.',
                'error' => $tokenData['details'],
            ];
        }
        $authorizationToken = $tokenData['token'];
        $order = Order::find($orderID);
        $user = Auth::user();
        $CustomerOrderId = $order->id . '-' . substr(Str::uuid()->toString(), 0, 8);
        $merchantTransactionId = substr(Str::uuid()->toString(), 0, 30);
        $paymentData = [
            'CustomerOrderId' => $CustomerOrderId,
            'merchantTransactionId' => $merchantTransactionId,
            'platform' => \App\utilities\Constant::EPS_TRX_TYPE['web'],
            'amount' => $amount,
            'customerName' => $user->name ?? 'Customer',
            'customerEmail' => $user->email ?? 'example@example.com',
            'customerAddress' => $user->address ?? 'N/A',
            'customerCity' => 'N/A',
            'customerState' => 'N/A',
            'customerPostcode' => '0000',
            'customerCountry' => 'BD',
            'customerPhone' => $user->mobile ?? '01700000000',
            'productName' => $order->id,
            'paymentReferance' => 'Order Reference EPS-' . $order->id,
        ];

        $paymentResponse = $this->makePayment($authorizationToken, $paymentData);
        if ($paymentResponse['status']) {
            return [
                'status' => true,
                'url' => $paymentResponse['data']['RedirectURL'],
            ];
        } else {
            return [
                'status' => false,
                'message' => $paymentResponse['message'],
            ];
        }
    }


    public function DropshipperOrderCheckout(Request $request)
    {

        $user = Auth::user();

        // nly dropshippers can place this type of order
        if (!$user || $user->usertype !== 'dropshipper') {
            return back()->with('error', 'Only dropshippers can order!');
        }

        // Request validation
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'selling_price' => 'required|numeric|min:1',
            'name' => 'required|string',
            'mobile' => ['required', 'regex:/(^(\+8801|8801|01|008801))[1|5-9]{1}(\d){8}$/'],
            'address' => 'required',

            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Price validation (selling price must be within min–max range)
        if ($request->selling_price < $product->min_price || $request->selling_price > $product->max_price) {
            return back()->with('error', "Selling price must be between {$product->min_price} and {$product->max_price}");
        }

        // Check stock availability
        if ($product->quantity < $request->qty) {
            return back()->with('error', 'Not enough stock available.');
        }

        // etch delivery zone charge
        $deliveryArea = DeliveryZone::find($request->delivery_area);
        $deliveryCharge = $deliveryArea->zone_charge ?? 0;

        // Calculate totals
        $orderTotal = $request->selling_price * $request->qty;
        $grandTotal = $orderTotal + $deliveryCharge;

        DB::beginTransaction();
        try {
            //Save shipping info
            $shipping = Shipping::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'address' => $request->address,
            ]);

            //Save payment info
            $payment = Payment::create([
                'payment_method' => $request->payment_method,
            ]);

            // ave order
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_id' => $shipping->id,
                'dropshipper_id' => auth()->id(),
                'payment_id' => $payment->id,
                'order_no' => $this->generateOrderNumber(),
                'delivery_charge' => $deliveryCharge,
                'order_total' => $orderTotal,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'order_payment' => 'Unpaid',
                'invoice_no' => $this->generateInvoiceNo(),
            ]);

            // save order details
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->qty,
                'buy_price' => $product->trade_price,     // Buy from vendor
                'sell_price' => $request->selling_price,   // Reseller selling price
                'dropshipper_profit' => $request->selling_price - $product->sale_price,
                'dropshipper_sell_price' => $request->selling_price,
                'vendor_id' => $product->user_id,
                'reseller_id' => $user->id,
                'color_id' => $request->color_id ?? null,
                'size_id' => $request->size_id ?? null,
            ]);
            // Reduce product stock
            $product->decrement('quantity', $request->qty);

            DB::commit();
            return redirect()->route('customer.order.details', $order->id)->with('success', 'Order placed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Function to generate invoice no
    private function generateInvoiceNo()
    {
        // Format: INV-YYYYMMDD-XXXX
        $datePart = now()->format('Ymd');
        $lastOrder = Order::latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;

        return 'INV-' . $datePart . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }




}
