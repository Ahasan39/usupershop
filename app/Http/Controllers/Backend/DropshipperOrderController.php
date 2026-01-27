<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use App\Models\MyShop;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DropshipperOrderController extends Controller
{
    protected $user_id;

    public function __construct()
    {
        $this->user_id = Auth::id();
    }

    /* ===============================
       DROPSHIPPER - PENDING ORDERS
    =============================== */
    public function pending()
    {
        return view('backend.dropshipper.order.pending-list');
    }

    public function dropshipperPendingPrintOrder($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);

        $pdf = Pdf::loadView('backend.dropshipper.order.print-dropshipper', $data);
        return $pdf->stream('print-dropshipper.pdf');
    }

    public function dropshipperPendingList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.pending-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperPendingResult($start, $length, $columns); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }
    public function dropshipperConfirmedList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.confirmed-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperOrderList($start, $length, $columns,'confirmed'); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }
    public function dropshipperPackagingList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.packaging-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperOrderList($start, $length, $columns,'packaging'); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }
    public function dropshipperShipmentList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.shipment-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperOrderList($start, $length, $columns,'shipment'); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }
    public function dropshipperCancelList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.cancel-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperCancelResult($start, $length, $columns); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }
    public function dropshipperReturnList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.order.return-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperReturnResult($start, $length, $columns); // You’ll define this query in your Order model

        $sn = $start + 1;
        foreach ($Result as $Res) {

            $DetailsRoute = route('dropshipper.orders.pending.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . ($Res->order_no);
            $order_total = ($Res->order_total);
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = ($Res->payment['payment_method'] == 'Bkash')
                ? 'Bkash [' . ($Res->payment['transaction_no']) . ']'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'pending')
                ? '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">Pending</span>'
                : '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'pending')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }

    /* ===============================
       DROPSHIPPER - DELIVERED ORDERS
    =============================== */
    public function delivered()
    {
        return view('backend.dropshipper.order.delivered-list');
    }

    public function dropshipperDeliveredList(Request $request)
    {

        if (!$request->ajax()) {
            return view('backend.dropshipper.order.delivered-list');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperDeliveredResult($start, $length, $columns);
        $sn = $start + 1;

        foreach ($Result as $Res) {
            $DetailsRoute = route('dropshipper.orders.delivered.details', $Res->id);

            $action = "<a title='Details' class='btn btn-sm btn-info' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";
            $order_no = 'ODR-#' . $Res->order_no;
            $order_total = $Res->order_total;
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = $Res->payment['payment_method'] == 'Bkash'
                ? 'Bkash'
                : $Res->payment['payment_method'];

            $status = ($Res->status == 'delivered')
                ? '<span style="background:#1BA160;color:#fff;padding:2px 8px;border-radius:3px;">Delivered</span>'
                : '<span style="background:#DD4F42;color:#fff;padding:2px 8px;border-radius:3px;">No Status</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'delivered')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'delivered')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }

    /* ===============================
       DROPSHIPPER - APPROVED / COMPLETED
    =============================== */
    public function dropshipperApprovedList(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Order::getDropshipperDeliveredResult($start, $length, $columns);
        $sn = $start + 1;

        foreach ($Result as $Res) {
            $DetailsRoute = route('dropshipper.orders.details', $Res->id);
            $action = "<a title='Details' class='btn btn-sm btn-primary' href='$DetailsRoute'><i class='fas fa-eye'></i> Details</a>";

            $order_no = 'ODR-#' . $Res->order_no;
            $order_total = $Res->order_total;
            $order_date = date('d-m-Y', strtotime($Res->created_at));

            $payment = $Res->payment['payment_method'] ?? 'N/A';

            $status = ($Res->status == 'delivered')
                ? '<span style="background:#71281b;color:#fff;padding:2px 8px;border-radius:3px;">Delivered</span>'
                : '<span style="background:red;color:#fff;padding:2px 8px;border-radius:3px;">Not Found</span>';

            $commission = $Res->commission ? $Res->commission . '%' : '0%';

            $Data[] = [
                'sn' => $sn,
                'order_no' => $order_no,
                'order_total' => $order_total,
                'payment_id' => $payment,
                'commission' => $commission,
                'order_date' => $order_date,
                'status' => $status,
                'action' => $action,
            ];

            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Order::where('status', 'delivered')->where('dropshipper_id', Auth::id())->count(),
            "iTotalDisplayRecords" => Order::where('status', 'delivered')->where('dropshipper_id', Auth::id())->count(),
            "aaData" => $Data,
        ];

        return response()->json($res);
    }

    /* ===============================
       DROPSHIPPER - ORDER DETAILS
    =============================== */
    public function dropshipperPendingDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.dropshipper.order.pending-order-details', $data);
    }

    public function dropshipperDeliveredDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.dropshipper.order.delivered-order-details', $data);
    }

    public function dropshipperDetails($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.dropshipper.order.order-details', $data);
    }
    public function details ($id)
    {
        $data['logo'] = Logo::first();
        $data['order'] = Order::find($id);
        return view('backend.dropshipper.order.order-details', $data);
    }

    public function orderCreate(Request $request)
    {

        $query = MyShop::with('product')->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $search . '%');
            });
        }

        $products = $query->take(36)->get();

        if (!$request->ajax()) {
            return view('backend.dropshipper.order.create-order', compact('products'));
        }

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
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
        return view('backend.dropshipper.order.pending-order-details', $data);
    }
    public function deliveredDetails ($id)
    {
         $data['logo'] = Logo::first();
        $data['order'] = Order::with([
            'order_details',
            'order_details.product',
            'order_details.product_color.color',
            'order_details.product_size.size',
            'area'
        ])->find($id);
        return view('backend.dropshipper.order.delivered-order-details', $data);
    }
}
