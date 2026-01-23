<?php

namespace App\Http\Controllers\Frontend;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Traits\BkashPaymentTrait;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;

class BkashPaymentGatewayController extends Controller
{
    use BkashPaymentTrait;
    public function BkashPaymentCreate(Request $request)
    {
        $contents = Cart::content(); // Retrieve all items in the cart
        // Generate a unique order number
        $order_no = 'ORDER-' . time(); // Example: Use timestamp for a unique order ID
    
        foreach ($contents as $content) {
            $order_details = new OrderDetail();
            $order_details->order_id = $order_no; // Assign the order number
            $order_details->product_id = $content->id; // Product ID
            $order_details->color_id = $content->options->color_id; // Color (optional attribute)
            $order_details->size_id = $content->options->size_id; // Size (optional attribute)
            $order_details->quantity = $content->qty; // Quantity
            $order_details->price = $content->price; // Price per unit
            $order_details->subtotal = $content->subtotal; // Total price for this item
            $order_details->shop_id = $content->options->shop_id; // Shop ID (from options)
            $order_details->save(); // Save the order detail to the database
        }
    
        // Clear the cart after processing the payment
        Cart::destroy();
    
        return response()->json(['success' => true, 'message' => 'Order processed successfully!']);
    }
    public function processPayment()
    {
        $grantToken = $this->grantToken();
         
        if (isset($grantToken['id_token'])) {
            $data = [
                'amount' => 10,
                'merchantInvoiceNumber' => '12-' . time(),
                'token_id' => $grantToken['id_token'],
            ];

            $paymentCreate = $this->paymentCreate($data);

            // if (isset($paymentCreate['paymentID'])) {
            //     $paymentID = $paymentCreate['paymentID'];
            //     $sessionToken = $grantToken['id_token'];

            //     $paymentExecute = $this->paymentExecute($paymentID, $sessionToken);

            //     if (isset($paymentExecute['statusCode']) && $paymentExecute['statusCode'] === '0000') {
            //         $paymentStatus = $this->checkPayment($paymentID, $sessionToken);

            //         return response()->json([
            //             'message' => 'Payment successful',
            //             'paymentStatus' => $paymentStatus,
            //         ]);
            //     }
            // }
        }

        return response()->json(['error' => 'Payment process failed'],500);
    }
}
