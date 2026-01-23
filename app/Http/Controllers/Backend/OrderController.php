<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedger;
use App\Models\Logo;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Traits\OrderAmountDistributionTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CourierService;

class OrderController extends Controller
{
    protected $courierService;

    public function __construct(CourierService $courierService)
    {
        $this->courierService = $courierService;
    }

    use OrderAmountDistributionTrait;
    public function pendingList()
    {

        return view('backend.order.pending-list');
    }
    public function AllOrderList()
    {
        return view('backend.order.all-order-list');
    }
    public function PendingOrderList()
    {
        return view('backend.order.order-list', ['status' => 'pending']);
    }

    public function ConfirmedOrderList()
    {
        return view('backend.order.order-list', ['status' => 'confirmed']);
    }

    public function DeliveredOrderList()
    {
        return view('backend.order.order-list', ['status' => 'delivered']);
    }

    public function PackagingOrderList()
    {
        return view('backend.order.order-list', ['status' => 'packaging']);
    }

    public function ReturnOrderList()
    {
        return view('backend.order.order-list', ['status' => 'return']);
    }

    public function CanceledOrderList()
    {
        return view('backend.order.order-list', ['status' => 'canceled']);
    }
    public function SellerOrderList()
    {
        return view('backend.order.seller-order-list');
    }
    // public function pList(Request $request)
    // {
    //     $draw = $request->input("draw");
    //     $length = $request->input("length");
    //     $start = $request->input("start");
    //     $columns = $request->input('columns');
    //     $Data = [];
    //     $Result = Order::getPendingResult($start, $length, $columns);
    //     $sn = $start + 1;
    //     foreach ($Result as $key => $Res) {
    //         $DetailsRoute = route('pending.orders.details', $Res->id);
    //         $DeleteRoute = route('orders.delete', $Res->id);
    //         $action = "<a title='Deatails' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";
    //         $action .= " <a title='Delete' class='btn btn-sm btn-danger' href='$DeleteRoute'><i class='fas fa-trash'></i> Delete</a>";
    //         $order_no = 'ODR-#' . ($Res->order_no);
    //         $order_total = ($Res->order_total);
    //         $newtime = strtotime($Res->created_at);
    //         $order_date = date('d-m-Y',$newtime);
    //         if ($Res->payment['payment_method'] == 'Bkash') {
    //             $payment = ($Res->payment['payment_method']);
    //         } else {
    //             if($Res->payment['payment_method'] === 'cod'){
    //                 $payment = 'Cash On Delivery';
    //             }
    //             else{
    //                 $payment = $Res->payment['payment_method'];
    //             }
    //         }
    //         if ($Res->status === 'confirmed') {
    //             $status = '<button type="button" class="btn btn-sm btn-primary">Confirmed</button>';
    //         } elseif($Res->status === 'canceled') {
    //             $status = '<button type="button" class="btn btn-sm btn-danger">Canceled</button>';
    //         }elseif($Res->status === 'packaging') {
    //             $status = '<button type="button" class="btn btn-sm btn-secondary">Packaging</button>';
    //         }elseif($Res->status === 'delivered') {
    //             $status = '<button type="button" class="btn btn-sm btn-success">Delivered</button>';
    //         }elseif($Res->status === 'return') {
    //             $status = '<button type="button" class="btn btn-sm btn-warning">Return</button>';
    //         }else{
    //             $status = '<button type="button" class="btn btn-sm btn-info">Pending</button>';
    //         }
    //         $Data[] = array(
    //             'sn' => $sn,
    //             'order_no' => $order_no,
    //             'order_total' => $order_total,
    //             'payment_id' => $payment,
    //             'order_date' => $order_date,
    //             'status' => $status,
    //             'action' => $action
    //         );
    //         $sn++;
    //     }
    //     $res = array(
    //         "draw" => $draw,
    //         "iTotalRecords" => Order::countResult($columns),
    //         "iTotalDisplayRecords" => Order::countResult($columns),
    //         "aaData" => $Data
    //     );

    //     return response()->json($res);
    // }
    public function deliveryList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDeliveredResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {
            $DeleteRoute = route('orders.delete', $Res->id);
            // $action = "<a title='Delete' class='btn btn-sm btn-danger' href='$DeleteRoute'><i class='fas fa-trash'></i> Delete</a>";
            $action = "N/A";
            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y', $newtime);

            if ($Res->payment['payment_method'] == 'Bkash') {
                $payment = ($Res->payment['payment_method']);
            } else {
                if ($Res->payment['payment_method'] === 'cod') {
                    $payment = 'Cash On Delivery';
                } else {
                    $payment = $Res->payment['payment_method'];
                }
            }
            if ($Res->status === 'confirmed') {
                $status = '<button type="button" class="btn btn-sm btn-primary">Confirmed</button>';
            } elseif ($Res->status === 'canceled') {
                $status = '<button type="button" class="btn btn-sm btn-danger">Canceled</button>';
            } elseif ($Res->status === 'packaging') {
                $status = '<button type="button" class="btn btn-sm btn-secondary">Packaging</button>';
            } elseif ($Res->status === 'delivered') {
                $status = '<button type="button" class="btn btn-sm btn-success">Delivered</button>';
            } elseif ($Res->status === 'return') {
                $status = '<button type="button" class="btn btn-sm btn-warning">Return</button>';
            } else {
                $status = '<button type="button" class="btn btn-sm btn-info">Pending</button>';
            }

            $Data[] = array(
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action
            );
            $sn++;
        }
        $res = array(
            "draw" => $draw,
            "iTotalRecords" => Order::countResult($columns),
            "iTotalDisplayRecords" => Order::countResult($columns),
            "aaData" => $Data
        );

        return response()->json($res);
    }
    public function deliveredList()
    {
        return view('backend.order.approved-list');
    }

    public function list(Request $request, $status = null)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];


        $query = Order::with('payment');

        if ($status) {
            $query->where('status', $status);
        }

        $totalRecords = $query->count();
        $Result = $query->offset($start)->limit($length)->latest()->get();

        $sn = $start + 1;

        foreach ($Result as $Res) {
            $DetailsRoute = route('orders.details', $Res->id);
            $action = "<a title='Details' class='btn btn-sm btn-primary' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = $Res->order_total;
            $order_date = $Res->created_at->format('d-m-Y');

            // Payment method
            if ($Res->payment && $Res->payment->payment_method == 'Bkash') {
                $payment = 'Bkash';
            } elseif ($Res->payment && $Res->payment->payment_method == 'cod') {
                $payment = 'Cash On Delivery';
            } else {
                $payment = $Res->payment->payment_method ?? 'N/A';
            }

            // Status button
            switch ($Res->status) {
                case 'confirmed':
                    $statusBtn = '<button type="button" class="btn btn-sm btn-primary">Confirmed</button>';
                    break;
                case 'canceled':
                    $statusBtn = '<button type="button" class="btn btn-sm btn-danger">Canceled</button>';
                    break;
                case 'packaging':
                    $statusBtn = '<button type="button" class="btn btn-sm btn-secondary">Packaging</button>';
                    break;
                case 'delivered':
                    $statusBtn = '<button type="button" class="btn btn-sm btn-success">Delivered</button>';
                    break;
                case 'return':
                    $statusBtn = '<button type="button" class="btn btn-sm btn-warning">Return</button>';
                    break;
                default:
                    $statusBtn = '<button type="button" class="btn btn-sm btn-info">Pending</button>';
                    break;
            }

            // Checkbox
            if ($Res->status != 'return') {
                $check_box = '<input type="checkbox" name="oder_id" class="checkedOrds" value="' . $Res->order_no . '" style="width:17px;height:17px;">';
            } else {
                $check_box = '<input type="checkbox" disabled="disabled" checked="checked" style="width:17px;height:17px;">';
            }

            $Data[] = [
                'check_box' => $check_box,
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'order_date' => $order_date,
                'status' => $statusBtn,
                'action' => $action
            ];

            $sn++;
        }

        $res = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $Data
        ];

        return response()->json($res);
    }

    public function vendorOrSellerOrderlist(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];
        $Result = Order::getSellerPendingResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {

            $DetailsRoute = route('orders.details', $Res->id);
            $action = "<a title='Deatails' class='btn btn-sm btn-primary' href='$DetailsRoute'><i class='fas fa-eye'></i>
                Details</a>";
            $order_no = 'ODR-#' . ($Res->id);
            $order_total = ($Res->order_total);
            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y', $newtime);

            if ($Res->payment['payment_method'] == 'Bkash') {
                $payment = ($Res->payment['payment_method']);
            } else {
                if ($Res->payment['payment_method'] === 'cod') {
                    $payment = 'Cash On Delivery';
                } else {
                    $payment = $Res->payment['payment_method'];
                }
            }
            if ($Res->status === 'confirmed') {
                $status = '<button type="button" class="btn btn-sm btn-primary">Confirmed</button>';
            } elseif ($Res->status === 'canceled') {
                $status = '<button type="button" class="btn btn-sm btn-danger">Canceled</button>';
            } elseif ($Res->status === 'packaging') {
                $status = '<button type="button" class="btn btn-sm btn-secondary">Packaging</button>';
            } elseif ($Res->status === 'delivered') {
                $status = '<button type="button" class="btn btn-sm btn-success">Delivered</button>';
            } elseif ($Res->status === 'return') {
                $status = '<button type="button" class="btn btn-sm btn-warning">Return</button>';
            } else {
                $status = '<button type="button" class="btn btn-sm btn-info">Pending</button>';
            }

            if ($Res->status != 'return') {
                $check_box = '<input type="checkbox" name="oder_id" class="checkedOrds" value="' . $Res->id . '" style="width:17px;height:17px;">';
            } else {
                $check_box = '<input type="checkbox" disabled="disabled" ' . $Res->id . ' checked="checked" style="width:17px;height:17px;">';
            }
            if (!empty($Res->shop_id)) {
                $shop = User::where('id', $Res->shop_id)->first();
                $shop_name = $shop->shop_name ?? 'Unknown Shop';
                $commission = isset($shop->commission) ? $shop->commission . '%' : '0%';
            } else {
                $shop = "Own Shop";
                $commission = "0%";
            }

            $Data[] = array(
                'check_box' => $check_box,
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'order_date' => $order_date,
                'shop' => $shop_name,
                'commission' => $commission,
                'status' => $status,
                'action' => $action
            );
            $sn++;
        }
        $res = array(
            "draw" => $draw,
            "iTotalRecords" => Order::countResultTotal($columns),
            "iTotalDisplayRecords" => Order::countResultTotal($columns),
            "aaData" => $Data
        );
        return response()->json($res);
    }

    public function pendingDetails($id)
    {
         $data['logo'] = Logo::first();
        $data['order'] = Order::with([
            'order_details',
            'order_details.product',
            'order_details.product_color.color',
            'order_details.product_size.size'
        ])->find($id);
        return view('backend.order.pending-order-details', $data);
    }
    public function printOrder($id)
    {
         $data['logo'] = Logo::first();
        $data['order'] = Order::with([
            'order_details',
            'order_details.product',
            'order_details.product_color.color',
            'order_details.product_size.size'
        ])->find($id);
        $pdf = Pdf::loadView('backend.order.print-pending-order-details', $data);
        return $pdf->stream('pending-order.pdf');
        // this returns a proper PDF response
    }

    public function details($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::with([
            'order_details',
            'order_details.product',
            'order_details.product_color.color',
            'order_details.product_size.size'
        ])->find($id);

        return view('backend.order.order-details', $data);
    }



    public function approve(Request $request)
    {
        $order = Order::find($request->id);
        $order->update(['status' => 'delivered']);
        return redirect()->route('orders.pending.list')->with('success', 'Data approved successfully');
    }


    // Status Update here............
    public function statusUpdate(Request $request)
    {
        $delivery_status = $request->status;
        $selectedODRs = $request->selectedODRs;

        if ($delivery_status == "" || $selectedODRs == "" || count($selectedODRs) == 0) {
            $res = ['message' => "Select Order Status and Order IDs !!!"];
            return response($res, 203)->header('Content-Type', 'application/json');
        }

        DB::beginTransaction();

        try {
            // multiple order_no হলেও প্রথম টা নিচ্ছে → better loop করা
            $orders = Order::whereIn('order_no', $selectedODRs)->get();

            if ($orders->isEmpty()) {
                DB::rollBack();
                $res = ['message' => "Something wrong !"];
                return response($res, 203)->header('Content-Type', 'application/json');
            }

            foreach ($orders as $order) {
                $order->status = $delivery_status;
                $order->save();

                if ($delivery_status === 'delivered') {
                    $this->order_amount_distribution($order);
                }
            }

            DB::commit();

            $res = ['message' => "Order status updated successfully !"];
            return response($res, 202)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            DB::rollBack();
            $res = ['message' => "Failed to update status. Please try again.", 'error' => $e->getMessage()];
            return response($res, 500)->header('Content-Type', 'application/json');
        }
    }

    private function order_amount_distribution(Order $order)
    {
        // order এর details আনো
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            if (!empty($detail->reseller_id)) {
                // আসল প্রোডাক্টের default sale_price বের করি
                $product = Product::find($detail->product_id);
                if (!$product)
                    continue;

                // ড্রপশিপারের বিক্রির টোটাল
                $dropshipperTotal = $order->order_total;

                // প্রোডাক্টের default sale price টোটাল
                $defaultTotal = $product->sale_price * $detail->quantity;

                // লাভ হিসাব
                $profit = $dropshipperTotal - $defaultTotal;

                if ($profit > 0) {
                    $dropshipper = User::find($detail->reseller_id);
                    if ($dropshipper) {
                        $dropshipper->increment('balance', $profit);
                    }
                }
            }
        }
    }


    public function VendorOrSellerstatusUpdate(Request $request)
    {
        $delivery_status = $request->status;
        $selectedODRs = $request->selectedODRs;

        if ($delivery_status == "" || $selectedODRs == "" || count($selectedODRs) == 0) {
            $res = ['message' => "Select Order Status and Order IDs !!!"];
            return response($res, 203)->header('Content-Type', 'application/json');
        }

        DB::beginTransaction();

        try {
            $order = Order::whereIn('order_no', $selectedODRs)->first();

            if (!$order) {
                DB::rollBack();
                $res = ['message' => "Something wrong !"];
                return response($res, 203)->header('Content-Type', 'application/json');
            }

            $order->status = $delivery_status;
            $order->save();

            if ($delivery_status === 'delivered') {
                $this->order_amount_distribution($order);
            }

            DB::commit();

            $res = ['message' => "Order status updated successfully !"];
            return response($res, 202)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            DB::rollBack();

            $res = ['message' => "Failed to update status. Please try again."];
            return response($res, 500)->header('Content-Type', 'application/json');
        }
    }

    // Order Commission cose here............
    public function orderCommission()
    {
        return view('backend.order.order_commission_list');
    }

    public function orderCommissionList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = CommissionLedger::getResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {
            $order_id = "ORD-" . ($Res->order_id);

            $newtime = strtotime($Res->entry_date);
            $comm_date = date('d-m-Y', $newtime);

            $reseller_id = ($Res->reseller_id);
            $debit_balance = ($Res->debit_balance);
            $credit_balance = ($Res->credit_balance);
            $payment_mood = ($Res->payment_mood);
            $reference = ($Res->reference);
            if ($reseller_id) {
                $shop = User::where('id', $reseller_id)->first();
                $shop_name = !empty($Res->shop) ? ($Res->shop->shop_name ?? 'Unknown Shop') : 'Unknown Shop';
            } else {
                $shop = "Own Shop";
            }


            $Data[] = array(
                'sn' => $sn,
                'order_id' => $order_id,
                'reseller_id' => $reseller_id,
                'debit_balance' => $debit_balance,
                'credit_balance' => $credit_balance,
                'payment_mood' => $payment_mood,
                'reference' => $reference,
                'comm_date' => $comm_date
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            "iTotalRecords" => CommissionLedger::count(),
            "iTotalDisplayRecords" => CommissionLedger::countResult($columns),
            "aaData" => $Data
        );

        return response()->json($res);
    }


    ///Seller wise commission code here.....
    public function sellerWiseCommission()
    {
        return view('backend.order.seller_wise_commission_list');
    }

    public function sellerWiseCommissionList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = CommissionLedger::getSellerWiseResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {

            //$DetailsRoute = route('orders.details', $Res->id);
            /* $DeleteRoute = route('orders.delete'); */

            $action = "<a href='#' title='Seller Commission' class='btn btn-sm btn-primary' onclick='openCommissionModal(" . $Res->reseller_id . ")' data=''><i class='fas fa-check'></i> Seller Commission</a>";

            $order_id = "ORD-" . ($Res->order_id);

            $newtime = strtotime($Res->entry_date);
            $comm_date = date('d-m-Y', $newtime);

            $reseller_id = ($Res->reseller_id);
            $total_debit = ($Res->total_debit);
            $total_credit = ($Res->total_credit);
            if ($reseller_id) {
                $shop = isset($Res->shop) && isset($Res->shop['name']) ? $Res->shop['name'] : 'Unknown Shop';
            } else {
                $shop = "Own Shop";
            }


            $Data[] = array(
                'sn' => $sn,
                'reseller_id' => $shop,
                'debit_balance' => $total_debit,
                'credit_balance' => $total_credit,
                'action' => $action
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            "iTotalRecords" => CommissionLedger::count(),
            "iTotalDisplayRecords" => CommissionLedger::countSellerWiseResult($columns),
            "aaData" => $Data
        );

        return response()->json($res);
    }

    public function addSellerPayment(Request $request)
    {

        $seller_id = $request->seller_id;
        $amount = $request->amount;
        $payment_mood = $request->payment_mood;
        $reference_no = $request->reference_no;
        $account_no = $request->account_no;
        $comments = $request->comments;


        if ($seller_id == "" || $amount == "") {
            $res = ['message' => "Please enter amount !!!"];
            return response($res, 203)->header('Content-Type', 'application/json');
        }

        if ($amount == 0) {
            $res = ['message' => "Please enter amount is greater than 0 !"];
            return response($res, 203)->header('Content-Type', 'application/json');
        }

        $data = array(
            'reseller_id' => $seller_id,
            'entry_date' => now(),
            'order_id' => 11,
            'debit_balance' => $amount,
            'payment_mood' => $payment_mood,
            'reference' => $reference_no,
            'created_at' => now(),
        );

        $dataInsert = CommissionLedger::insert($data);

        if ($dataInsert) {
            $seller_info = User::find($seller_id);
            $commission_balance = $seller_info->balance - $amount;
            $updateBalance = User::where('id', $seller_id)->update(['balance' => $commission_balance]);
            if ($updateBalance) {
                $res = ['message' => "Payment submitted successfully !"];
                return response($res, 202)->header('Content-Type', 'application/json');
            } else {
                $res = ['message' => "Something wrong !"];
                return response($res, 203)->header('Content-Type', 'application/json');
            }
        }
    }
    public function delete($id)
    {
        $orders = Order::find($id);
        $orders->delete();
        return redirect()->route('orders.all.list')->with('success', 'Data deleted successfully !');
    }
    /**
     * Assign single order to courier
     */
    public function assignCourier(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'courier_id' => 'required|string',
            'priority' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::where('order_no', $request->order_id)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return response()->json(['message' => 'Only pending or confirmed orders can be assigned to courier'], 400);
            }

            // Send to courier API
            $courierResult = $this->courierService->sendToCourier(
                $order,
                $request->courier_id,
                $request->priority ?? 'normal',
                $request->notes
            );

            if (!$courierResult['success']) {
                throw new \Exception($courierResult['message']);
            }

            // Update order with courier information
            $order->update([
                'courier_id' => $request->courier_id,
                'courier_priority' => $request->priority ?? 'normal',
                'courier_notes' => $request->notes,
                'courier_tracking_id' => $courierResult['tracking_id'] ?? null,
                'courier_response' => json_encode($courierResult['response'] ?? []),
                'status' => 'packaging', // Change status to packaging when sent to courier
                'courier_assigned_at' => now(),
                'courier_assigned_by' => Auth::id()
            ]);

            // Log the courier assignment
            Log::info('Order assigned to courier via API', [
                'order_no' => $order->order_no,
                'courier_id' => $request->courier_id,
                'tracking_id' => $courierResult['tracking_id'] ?? null,
                'assigned_by' => Auth::id(),
                'assigned_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => $courierResult['message'],
                'tracking_id' => $courierResult['tracking_id'] ?? null,
                'order' => $order
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Courier assignment failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
                'courier_id' => $request->courier_id
            ]);

            return response()->json([
                'message' => 'Failed to assign order to courier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assign orders to courier
     */
    public function bulkAssignCourier(Request $request)
    {
        $request->validate([
            'courier_id' => 'required|string',
            'orders' => 'required|array|min:1',
            'orders.*' => 'exists:orders,order_no',
            'priority' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $orders = Order::whereIn('order_no', $request->orders)
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();

            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No eligible orders found'], 400);
            }

            // Send orders to courier API
            $courierResult = $this->courierService->bulkSendToCourier(
                $orders,
                $request->courier_id,
                $request->priority ?? 'normal'
            );

            // Update successful orders
            foreach ($orders as $order) {
                $orderResult = collect($courierResult['details'])->firstWhere('order_no', $order->order_no);

                if ($orderResult && $orderResult['result']['success']) {
                    $order->update([
                        'courier_id' => $request->courier_id,
                        'courier_priority' => $request->priority ?? 'normal',
                        'courier_tracking_id' => $orderResult['result']['tracking_id'] ?? null,
                        'courier_response' => json_encode($orderResult['result']['response'] ?? []),
                        'status' => 'packaging',
                        'courier_assigned_at' => now(),
                        'courier_assigned_by' => Auth::id()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => $courierResult['message'],
                'details' => $courierResult['details']
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk courier assignment failed', [
                'error' => $e->getMessage(),
                'courier_id' => $request->courier_id,
                'orders_count' => count($request->orders)
            ]);

            return response()->json([
                'message' => 'Failed to assign orders to courier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track courier order
     */
    public function trackCourierOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);

        $order = Order::where('order_no', $request->order_id)->first();

        if (!$order || !$order->courier_id || !$order->courier_tracking_id) {
            return response()->json(['message' => 'Order not found or not assigned to courier'], 404);
        }

        $trackingResult = $this->courierService->trackOrder(
            $order->courier_id,
            $order->courier_tracking_id
        );

        if ($trackingResult['success']) {
            return response()->json([
                'message' => 'Tracking information retrieved',
                'tracking_data' => $trackingResult['data'],
                'order' => $order
            ], 200);
        }

        return response()->json([
            'message' => $trackingResult['message']
        ], 400);
    }

    /**
     * Get courier cities (for Pathao)
     */
    public function getCourierCities(Request $request)
    {
        $courierId = $request->get('courier_id');

        if ($courierId === 'pathao') {
            $cities = $this->courierService->getPathaoCities();
            return response()->json(['cities' => $cities]);
        }

        return response()->json(['cities' => []], 404);
    }

    /**
     * Get courier zones (for Pathao)
     */
    public function getCourierZones(Request $request)
    {
        $courierId = $request->get('courier_id');
        $cityId = $request->get('city_id');

        if ($courierId === 'pathao' && $cityId) {
            $zones = $this->courierService->getPathaoZones($cityId);
            return response()->json(['zones' => $zones]);
        }

        return response()->json(['zones' => []], 404);
    }
    /**
     * Update pList method to include checkbox column
     */
    public function pList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];
        $Result = Order::getPendingResult($start, $length, $columns);
        $sn = $start + 1;

        foreach ($Result as $key => $Res) {
            $DetailsRoute = route('orders.details', $Res->id);
            $DeleteRoute = route('orders.delete', $Res->id);

            // Updated action buttons with courier assignment
            $action = "<a title='Details' class='btn btn-sm btn-info mr-1' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            if ($Res->courier_id) {
                // Show courier info if already assigned
                $courierName = ucfirst(str_replace('_', ' ', $Res->courier_id));
                $action .= "<span class='badge badge-success' title='Assigned to {$courierName}'><i class='fas fa-shipping-fast'></i> {$courierName}</span>";
            } else {
                // Show courier assignment button if not assigned
                $action .= "<button title='Send to Courier' class='btn btn-sm btn-success courier-btn ml-1' onclick='openCourierModal(" . json_encode($Res) . ")'>";
                $action .= "<i class='fas fa-shipping-fast'></i> Courier</button>";
            }

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $newtime = strtotime($Res->created_at);
            $order_date = date('d-m-Y', $newtime);

            if ($Res->payment['payment_method'] == 'Bkash') {
                $payment = ($Res->payment['payment_method']);
            } else {
                if ($Res->payment['payment_method'] === 'cod') {
                    $payment = 'Cash On Delivery';
                } else {
                    $payment = $Res->payment['payment_method'];
                }
            }

            if ($Res->status === 'confirmed') {
                $status = '<button type="button" class="btn btn-sm btn-primary">Confirmed</button>';
            } elseif ($Res->status === 'canceled') {
                $status = '<button type="button" class="btn btn-sm btn-danger">Canceled</button>';
            } elseif ($Res->status === 'packaging') {
                $status = '<button type="button" class="btn btn-sm btn-secondary">Packaging</button>';
            } elseif ($Res->status === 'delivered') {
                $status = '<button type="button" class="btn btn-sm btn-success">Delivered</button>';
            } elseif ($Res->status === 'return') {
                $status = '<button type="button" class="btn btn-sm btn-warning">Return</button>';
            } else {
                $status = '<button type="button" class="btn btn-sm btn-info">Pending</button>';
            }

            // Add checkbox for bulk selection
            $checkbox = '<input type="checkbox" class="order-checkbox" value="' . $Res->order_no . '" data-order=\'' . json_encode([
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'order_date' => $order_date
            ]) . '\'>';

            $Data[] = array(
                'checkbox' => $checkbox,
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action
            );
            $sn++;
        }

        $res = array(
            "draw" => $draw,
            "iTotalRecords" => Order::countResult($columns),
            "iTotalDisplayRecords" => Order::countResult($columns),
            "aaData" => $Data
        );

        return response()->json($res);
    }

    public function courierOrders()
    {
        return view('backend.order.courier_orders');
    }

    public function courierOrdersData(Request $request)
    {
        $orders = Order::whereNotNull('courier_id')
            ->where('status', 'confirmed')
            ->latest();

        return datatables()->of($orders)
            ->addIndexColumn() // for SN
            ->editColumn('order_total', fn($row) => number_format($row->order_total, 2))
            ->editColumn('order_date', fn($row) => $row->created_at->format('d M, Y h:i A'))
            ->editColumn('status', fn($row) => ucfirst($row->status))
            ->addColumn('action', function ($row) {
                return '<a href="' . route('orders.details', $row->id) . '" class="btn btn-sm btn-info">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



}