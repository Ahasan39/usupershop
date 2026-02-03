<?php

namespace App\Http\Controllers\Frontend;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Traits\BkashPaymentTrait;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Session;

class BkashPaymentGatewayController extends Controller
{
    use BkashPaymentTrait;

    public function BkashPaymentCreate(Request $request)
    {
        // Placeholder or legacy method
        return response()->json(['success' => true, 'message' => 'Order processed successfully!']);
    }

    public function callback(Request $request)
    {
        $status = $request->input('status');
        $paymentID = $request->input('paymentID');

        // Grant token is needed for almost everything
        $grantToken = $this->grantToken();
        if (!isset($grantToken['id_token'])) {
            return redirect()->route('frontend.home')->with('error', 'Token Generation Failed');
        }

        if ($status == 'cancel' || $status == 'failure') {
            // Try to find the order to log the user back in (session loss fix)
            $check = $this->checkPayment($paymentID, $grantToken['id_token']);
            $merchantInvoiceNumber = $check['data']['merchantInvoiceNumber'] ?? ($check['data']['merchantInvoice'] ?? null);
            
            if ($merchantInvoiceNumber) {
                $order_id = explode('-', $merchantInvoiceNumber)[0];
                $order = Order::find($order_id);
                if ($order && !auth()->check()) {
                    auth()->loginUsingId($order->user_id);
                }
            }
            return redirect('/')->with('error', 'Payment Cancelled or Failed');
        }

        if ($status == 'success') {
            $execution = $this->paymentExecute($paymentID, $grantToken['id_token']);

            if ($execution['status']) {
                $merchantInvoiceNumber = $execution['data']['merchantInvoiceNumber'];
                $order_id = explode('-', $merchantInvoiceNumber)[0];

                $order = Order::find($order_id);
                if ($order) {
                    // Log user back in if lost
                    if (!auth()->check()) {
                        auth()->loginUsingId($order->user_id);
                    }

                    $order->payment_method = 'Bkash';
                    $order->status = 'pending'; 
                    $order->save();

                    if ($order->payment_id) {
                         $payment = Payment::find($order->payment_id);
                         if ($payment) {
                             $payment->payment_status = 'paid';
                             $payment->transaction_id = $execution['data']['trxID'];
                             $payment->save();
                         }
                    }
                    
                    Cart::destroy();
                    Session::forget(['coupon_discount', 'shipping_id', 'delivery_charge', 'areaID']);

                    if (auth()->user() && (auth()->user()->usertype == 'seller' || auth()->user()->usertype == 'dropshipper')) {
                        return redirect()->route('seller.customer.order.list')->with('success', 'Payment Successful! Transaction ID: ' . $execution['data']['trxID']);
                    }

                    return redirect()->route('customer.order.list')->with('success', 'Payment Successful! Transaction ID: ' . $execution['data']['trxID']);
                } else {
                     return redirect('/')->with('error', 'Order not found after payment!');
                }
            } else {
                 return redirect('/')->with('error', $execution['message']);
            }
        }
        
        return redirect('/')->with('error', 'Unknown Payment Status');
    }

    public function processPayment()
    {
        return response()->json(['error' => 'Use DashboardController flow'], 500);
    }
}
