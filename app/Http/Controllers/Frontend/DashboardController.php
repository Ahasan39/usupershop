<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Logo;

use App\Models\User;
use App\Models\Order;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Traits\EPSGatewayTrait;
use App\Traits\BkashPaymentTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;

class DashboardController extends Controller
{
    use BkashPaymentTrait;
    use EPSGatewayTrait;
    public function dashboard()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['user'] = Auth::user();
        return view('frontend.single_page.customer-dashboard', $data);
    }

    public function editProfile()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['editData'] = User::find(Auth::user()->id);
        return view('frontend.single_page.customer-edit-profile', $data);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'mobile' => ['required', 'unique:users,mobile,' . $user->id, 'regex:/(^(\+8801|8801|01|008801))[1|5-9]{1}(\d){8}$/'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->gender = $request->gender;
        if ($request->file('image')) {
            $file = $request->file('image');
            @unlink(public_path('upload/user_images/' . $user->image));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);
            $user['image'] = $filename;
        }
        $user->save();
        return redirect()->route('dashboard')->with('success', 'Profile update successfully !!!');
    }

    public function passwordChange()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        return view('frontend.single_page.customer-password-change', $data);
    }

    public function passwordUpdate(Request $request)
    {
        if (Auth::attempt(['id' => Auth::user()->id, 'password' => $request->current_password])) {
            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->new_password);
            $user->save();
            return redirect()->route('dashboard')->with('success', 'Your password changed successfully !!!');
        } else {
            return redirect()->back()->with('error', 'Sorry! Your current password does not match !!!');
        }
    }

    public function payment()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        return view('frontend.single_page.customer-payment', $data);
    }

    public function paymentStore(Request $request)
    {
        if (is_null($request->product_id)) {
            return redirect()->back()->with('message', 'Please add any product for payment!');
        }

        $request->validate([
            'payment_method' => 'required',
        ]);

        try {
            $deliverycharge = Session::get('delivery_charge', 0);
            $del = intval($deliverycharge);

            // Validate stock before starting transaction
            foreach (Cart::content() as $content) {
                $product = Product::find($content->id);
                if (!$product || $product->quantity < $content->qty) {
                    return redirect()
                        ->route('product.list')
                        ->with('error', "Product '{$product->name}' is out of stock or has insufficient quantity.");
                }
            }

            DB::transaction(function () use ($request, &$orderID, &$orderPaymentStatus, $del) {
                // Create Payment
                $payment = new Payment();
                $payment->payment_method = $request->payment_method;
                $payment->save();

                // Create Order
                $order = Order::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'shipping_id' => Session::get('shipping_id'),
                        'payment_id' => $payment->id,
                        'invoice_no' => $payment->id,
                    ],
                    [
                        'delivery_charge' => $del,
                        'order_no' => $this->generateOrderNumber(),
                        'coupon_discount' => Session::get('coupon_discount', 0),
                        'order_total' => $request->order_total,
                        'grand_total' => $request->grand_total,
                        'status' => 'pending',
                    ],
                );

                $orderID = $order->id;
                $invoice = $order->id . '_' . Carbon::now()->format('YmdHis');
                $order->update(['invoice_no' => $invoice]);
                $orderPaymentStatus = $order->order_payment;

                // Create Order Details
                foreach (Cart::content() as $content) {
                    $product = Product::find($content->id);

                    if ($product && $product->quantity >= $content->qty) {
                        
                        // Vendor commission logic
                        $user = User::find($product->user_id);
                        if ($user && $user->usertype == 'vendor' && $user->payment_status == 2) {
                            $orderAmt = $request->order_total - $del;
                            $sellerCommission = $orderAmt * 0.2;
                            $vendorAmt = $orderAmt - $sellerCommission;
                            $user->balance = ($user->balance ?? 0) + $vendorAmt;
                            $user->save();
                        }

                        // Update stock
                        $product->quantity -= $content->qty;
                        $product->save();

                        // Save order detail
                        OrderDetail::create([
                            'order_id' => $orderID,
                            'product_id' => $content->id,
                            'color_id' => $content->options->color_id ?? null,
                            'color_name' => $content->options->color_name ?? null,
                            'size_id' => $content->options->size_id ?? null,
                            'size_name' => $content->options->size_name ?? null,
                            'quantity' => $content->qty,
                        ]);
                    }
                }
            });

            DB::commit();
            $coupon = Session::get('coupon_discount', 0);

            // Handle Payment
            $amount = $request->order_total + $del - $coupon;
            if ($request->payment_method == 'Bkash') {
                $payment_url = $this->processBkashPayment($amount, $orderID);
            } elseif ($request->payment_method == 'EPS') {
                $payment_url = $this->processEPSpaymenyGateway($amount, $orderID);
            } elseif ($request->payment_method == 'Cash on Delivery') {
                $payment_url = $this->processBkashPayment($del, $orderID);
            } else {
                return redirect()->back()->with('error', 'Invalid payment method selected!');
            }

            $this->clearCartAndSession();

            if ($payment_url['status'] === true) {
                return redirect($payment_url['url']);
            } else {
                return redirect()
                    ->route('product.list')
                    ->with('error', $payment_url['message'] ?? 'Something went wrong!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Processing Failed: ' . $e->getMessage());
            return redirect()->route('shop.page')->with('error', 'Something went wrong during order processing. Please try again.');
        }
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
    private function clearCartAndSession()
    {
        Cart::destroy();
        Session::forget(['coupon_discount', 'shipping_id', 'delivery_charge', 'areaID']);
    }
    public function sellerPaymentStore(Request $request)
    {
        if ($request->product_id == null) {
            return redirect()->back()->with('message', 'Please add any product for payment !!!');
        } else {
            $this->validate($request, [
                'payment_method' => 'required',
            ]);
            try {
                $deliverycharge = Session::get('delivery_charge', 0);
                $del = intval($deliverycharge);
                foreach (Cart::content() as $content) {
                    $product = Product::find($content->id);
                    if (!$product || $product->quantity < $content->qty) {
                        return redirect()
                            ->route('product.list')
                            ->with('error', "Product '{$product->name}' is out of stock or has insufficient quantity.");
                    }
                }
                DB::transaction(function () use ($request, &$orderID, &$del) {
                    $payment = new Payment();
                    $payment->payment_method = $request->payment_method;
                    $payment->save();
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->shipping_id = Session::get('shipping_id');
                    $order->payment_id = $payment->id;
                    $order->delivery_charge = $del;
                    $order_data = Order::orderBy('id', 'DESC')->first();
                    if ($order_data == null) {
                        $firstReg = '0';
                        $order_no = $firstReg + 1;
                    } else {
                        $order_data = Order::orderBy('id', 'DESC')->first()->order_no;
                        $order_no = $order_data + 1;
                    }
                    $order->order_no = $order_no;
                    $order->coupon_discount = Session::get('coupon_discount', 0);
                    $order->order_total = $request->order_total;
                    $order->status = 'pending';
                    $order->save();
                    $orderID = $order->id;
                    $contents = Cart::content();
                    foreach ($contents as $content) {
                        $product = Product::find($content->id);
                        if ($product && $product->quantity >= $content->qty) {
                            $userId = $product->user_id;
                            $user = User::where('id', $userId)->first();
                            if ($user && $user->usertype == 'vendor') {
                                $orderAmt = $request->order_total - $del;
                                $sellerCommission = $orderAmt * 0.2; // 20% commission
                                $vendorAmt = $orderAmt - $sellerCommission;
                                // Ensure balance is initialized
                                $user->balance = ($user->balance ?? 0) + $vendorAmt;
                                $user->save();
                            }
                        }
                        // Update stock
                        $product->quantity -= $content->qty;
                        $product->save();
                        $shop_id = $content->options->shopID;
                        OrderDetail::create([
                            'order_id' => $orderID,
                            'product_id' => $content->id,
                            'color_id' => $content->options->color_id,
                            'color_name' => $content->options->color_name,
                            'size_id' => $content->options->size_id,
                            'size_name' => $content->options->size_name,
                            'quantity' => $content->qty,
                        ]);
                        $orderShopSaveData = Order::find($orderID);
                        $orderShopSaveData->update([
                            'shop_id' => $shop_id,
                        ]);
                    }
                });
                DB::commit();
                $coupon = Session::get('coupon_discount', 0);
                // Handle Payment Methods
                if ($request->payment_method == 'Bkash') {
                    $payment_url = $this->processBkashPayment($request->order_total + $del - $coupon, $orderID);
                } else {
                    $payment_url = $this->processBkashPayment($del, $orderID);
                }
                $this->clearCartAndSession();
                if ($payment_url['status'] == true) {
                    return redirect($payment_url['url']);
                } else {
                    return redirect()->route('product.list')->with('error', 'Something error!');
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                Log::error('Order Processing Failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Something went wrong during order processing. Please try again.');
            }
        }
    }
    private function getAreaID()
    {
        $string = Session::get('areaID');
        if ($string) {
            $exString = explode('-', $string);
            return isset($exString[0]) && is_numeric($exString[0]) ? (int) $exString[0] : 4;
        }
        return 4; // Default value
    }

    /**
     * Generate the next order number
     */
    private function generateOrderNumber()
    {
        $lastOrder = Order::orderBy('id', 'DESC')->first();
        return $lastOrder ? $lastOrder->order_no + 1 : 1;
    }

    /**
     * Process Bkash Payment
     */
    private function processBkashPayment($orderTotal, $orderID)
    {
        // Fetch Grant Token
        $grantToken = $this->grantToken();
        if (isset($grantToken['id_token'])) {
            $data = [
                'amount' => $orderTotal,
                'merchantInvoiceNumber' => $orderID . '-' . time(),
                'token_id' => $grantToken['id_token'],
            ];
            // Create Payment Request
            $paymentCreate = $this->paymentCreate($data);
            if ($paymentCreate['status']) {
                if (isset($paymentCreate['data']['bkashURL'])) {
                    // Redirect to Bkash Payment Page
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

    public function orderList()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['orders'] = Order::where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('frontend.single_page.customer-order', $data);
    }

    public function orderDetails($id)
    {
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $orderData = Order::find($id);
        $data['orders'] = Order::where('id', $orderData->id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($data['orders'] == false) {
            return redirect()->back()->with('error', 'Do not try to be over smart !!!');
        } else {
            $data['logo'] = Logo::first();
            $data['contact'] = Contact::first();
            $data['order'] = Order::with(['order_details'])
                ->where('id', $orderData->id)
                ->where('user_id', Auth::user()->id)
                ->first();
            return view('frontend.single_page.customer-order-details', $data);
        }
    }

    // Seller Code here................
    public function sellerDashboard()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['user'] = Auth::user();
        return view('frontend.seller_shop.seller-customer-dashboard', $data);
    }

    public function sellerEditProfile()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['editData'] = User::find(Auth::user()->id);
        return view('frontend.seller_shop.seller-customer-edit-profile', $data);
    }

    public function sellerUpdateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'mobile' => ['required', 'unique:users,mobile,' . $user->id, 'regex:/(^(\+8801|8801|01|008801))[1|5-9]{1}(\d){8}$/'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->gender = $request->gender;
        if ($request->file('image')) {
            $file = $request->file('image');
            @unlink(public_path('upload/user_images/' . $user->image));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);
            $user['image'] = $filename;
        }
        $user->save();
        return redirect()->route('seller.customer.dashboard')->with('success', 'Profile update successfully !!!');
    }

    public function sellerPasswordChange()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        return view('frontend.seller_shop.seller-customer-password-change', $data);
    }

    public function sellerPasswordUpdate(Request $request)
    {
        if (Auth::attempt(['id' => Auth::user()->id, 'password' => $request->current_password])) {
            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->new_password);
            $user->save();
            return redirect()->route('seller.customer.dashboard')->with('success', 'Your password changed successfully !!!');
        } else {
            return redirect()->back()->with('error', 'Sorry! Your current password does not match !!!');
        }
    }

    public function sellerPayment()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        return view('frontend.seller_shop.seller-customer-payment', $data);
    }

    public function sellerOrderList()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $data['orders'] = Order::where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->get();
        return view('frontend.seller_shop.seller-customer-order', $data);
    }

    public function sellerOrderDetails($id)
    {
        $data['categories'] = Product::select('category_id')->groupBy('category_id')->get();
        $orderData = Order::find($id);
        $data['orders'] = Order::where('id', $orderData->id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($data['orders'] == false) {
            return redirect()->back()->with('error', 'Do not try to be over smart !!!');
        } else {
            $data['logo'] = Logo::first();
            $data['contact'] = Contact::first();
            $data['order'] = Order::with(['order_details'])
                ->where('id', $orderData->id)
                ->where('user_id', Auth::user()->id)
                ->first();
            return view('frontend.seller_shop.seller-customer-order-details', $data);
        }
    }
}
