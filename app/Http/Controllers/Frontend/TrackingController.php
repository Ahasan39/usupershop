<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function orderTrackNow(Request $request,$id)
    {
        $order = Order::with(['users','shipping'])->where('user_id', auth()->user()->id)->where('id', $id)->first();
        if ($order) {
            $orderItems = OrderDetail::with('product')->where('order_id',$order->id)->orderBy('id','DESC')->get();
            return view('frontend.order-track', compact('order','orderItems'));
        } else {
            $notification = array(
                'message' => 'Order Not Found',
                'alert-type' => 'error'
            );
            return Redirect()->back()->with($notification);
        }
    }
    public function orderTrackSubmit(Request $request)
    {
        // Allow public tracking by Invoice Number (acting as a tracking ID)
        $invoice_no = trim($request->invoice_no);
        
        $order = Order::with(['users', 'shipping'])
            ->where('invoice_no', $invoice_no)
            ->first();
            
        // Check if the order exists
        if ($order) {
            // Load order items and associated products
            $orderItems = OrderDetail::with('product')
                ->where('order_id', $order->id)
                ->orderBy('id', 'DESC')
                ->get();
                
            // Display the order details view
            return view('frontend.order-track', compact('order', 'orderItems'));
        } else {
            return redirect()->back()->with('error', 'Order not found or you do not have permission to view this order.');
        }
    }
}
