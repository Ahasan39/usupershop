<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerOrderController extends Controller
{

    public function __construct()
    {
       $this->user_id = Auth::id();
    }

    public function sellerPending()
    {
        return view('backend.seller.order.pending-list');
    }
    public function sellerPendingPrintOrder($id){
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        $pdf = Pdf::loadView('backend.seller.order.print-seller', $data);
        return $pdf->stream('print-seller.pdf'); 
    }
    public function sellerPendingList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getSellerPendingResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res){

            $DetailsRoute = route('seller.pending.orders.details', $Res->id);
            /* $DeleteRoute = route('coupons.delete'); */

            $action = "<a title='Deatails' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";
            //$action .= " <a title='Approve' class='btn btn-sm btn-primary' href='#'><i class='fas fa-check'></i> Approve</a>";
            //$action .= " <a title='Delete' class='btn btn-sm btn-danger' href='#'><i class='fas fa-trash'></i> Delete</a>";

            $order_no = 'ODR-#'.($Res->order_no);

            $order_total = ($Res->order_total);

            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y',$newtime);

            if ($Res->payment['payment_method'] == 'Bkash'){
                $payment = ($Res->payment['payment_method']) . ' [' . ($Res->payment['transaction_no']) . ']';
            } else {
                $payment = $Res->payment['payment_method'];
            }

            if ($Res->status == 'pending'){
                $status = '<span style="background:#DD4F42;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;">Pending</span>';
            } else {
                $status = '<span style="background:#1BA160;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;">No Status</span>';
            }

            if($Res->commission !=NULL){
                $commission = $Res->commission.'%';
            }else{
                $commission = "0%";
            }

            $Data[] = array(
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            /* "iTotalRecords" => Order::count(),
            "iTotalDisplayRecords" => Order::countResult($columns), */
            "iTotalRecords" => Order::where('status','=','pending')->where('shop_id','=', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status','=','pending')->where('shop_id','=', Auth::id())->count(),
            "aaData" => $Data
        );

        return response()->json($res);
    }
    public function sellerDeliveredList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];
        $Result = Order::getSellerDeliveredResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res){
            $DetailsRoute = route('seller.delivered.orders.details', $Res->id);
            /* $DeleteRoute = route('coupons.delete'); */
            $action = "<a title='Deatails' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";
            $order_no = 'ODR-#'.($Res->order_no);

            $order_total = ($Res->order_total);

            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y',$newtime);

            if ($Res->payment['payment_method'] == 'Bkash'){
                $payment = ($Res->payment['payment_method']) ;
            } else {
                $payment = $Res->payment['payment_method'];
            }

            if ($Res->status == 'delivered'){
                $status = '<span style="background:#1BA160;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;">Delivered</span>';
            } else {
                $status = '<span style="background:#DD4F42;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;">No Status</span>';
            }

            if($Res->commission !=NULL){
                $commission = $Res->commission.'%';
            }else{
                $commission = "0%";
            }

            $Data[] = array(
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            /* "iTotalRecords" => Order::count(),
            "iTotalDisplayRecords" => Order::countResult($columns), */
            "iTotalRecords" => Order::where('status','=','delivered')->where('shop_id','=', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status','=','delivered')->where('shop_id','=', Auth::id())->count(),
            "aaData" => $Data
        );

        return response()->json($res);
    }
    public function sellerDelivered()
    {
        return view('backend.seller.order.delivered-list');
    }

    public function sellerApprovedList(Request $request)
    { 
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getSellerDeliveredResult($start,$length,$columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {
            // dd($Res);
            $DetailsRoute = route('seller.orders.details', $Res->id);
            /* $DeleteRoute = route('coupons.delete'); */
            $action = "<a title='Deatails' class='btn btn-sm btn-primary' href='$DetailsRoute'><i class='fas fa-eye'></i>
                Details</a>";
            $order_no = 'ODR-#' . ($Res->order_no);

            $order_total = ($Res->order_total);

            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y',$newtime);

            if ($Res->payment['payment_method'] == 'Bkash'){
                $payment = ($Res->payment['payment_method']);
            } else {
                $payment = $Res->payment['payment_method'];
            }

            if ($Res->status == 'delivered'){
                $status = '<span
                    style="background:#71281b;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;"> Delivered</span>';
            } else{
                $status = '<span
                    style="background:red;color: #fff;padding:2px 8px 6px 8px;border-radius:3px;">Not Found</span>';
            }

            if($Res->commission !=NULL){
                $commission = $Res->commission.'%' ?? 'N/A';
            }else{
                $commission = "0%";
            }

            $Data[] = array(
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            /* "iTotalRecords" => Order::count(),
            "iTotalDisplayRecords" => Order::countResult($columns), */
            "iTotalRecords" => Order::where('status','=','delivered')->where('shop_id','=', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status','=','delivered')->where('shop_id','=', Auth::id())->count(),
            "aaData" => $Data
        );

        return response()->json($res);
    }

    public function sellerPendingDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.seller.order.pending-order-details', $data);
    }
    public function sellerDeliveredDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.seller.order.pending-order-details', $data);
    }
    public function sellerDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.seller.order.order-details', $data);
    }

    /* public function approve(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = '1';
        $order->save();

        return redirect()->route('orders.pending.list')->with('success', 'Data approved successfully');
    } */
}
