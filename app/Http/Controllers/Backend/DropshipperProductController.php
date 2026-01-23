<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Country;
use App\Models\MyShop;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductSubImage;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DropshipperProductController extends Controller
{
    public function shopkeeper_product()
    {
        $data['products'] = Product::all();
        return view('backend.dropshipper.product.shopkeeper_product', $data);
    }

    public function SearchProducts(Request $request)
    {
        $keyword = $request->product_search;
        $data['products'] = Product::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('sku', 'LIKE', '%' . $keyword . '%')
            ->orWhere('short_desc', 'LIKE', '%' . $keyword . '%')
            ->get();
        return view('backend.dropshipper.product.shopkeeper_product', $data);
    }

    public function MyWallets()
    {
        $profile = Wallet::select(
            'wallets.id',
            'users.id as user_id',
            'wallets.user_id',
            'wallets.mobile_no',
            'wallets.payment_type',
            'wallets.transaction_status',
            'users.name',
            'users.balance',
            'profile_verifies.nid_no',
            'profile_verifies.front_image'
        )
            ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
            ->leftJoin('profile_verifies', 'profile_verifies.user_id', '=', 'users.id')
            ->where('users.id', auth()->user()->id)
            ->first();

        return view('backend.dropshipper.user.wallets', compact('profile'));
    }

    public function MyWalletsVerified()
    {
        $profile = Wallet::select(
            'wallets.id',
            'users.id as user_id',
            'wallets.user_id',
            'wallets.mobile_no',
            'wallets.payment_type',
            'wallets.transaction_status',
            'users.name',
            'users.balance',
            'profile_verifies.nid_no',
            'profile_verifies.front_image'
        )
            ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
            ->leftJoin('profile_verifies', 'profile_verifies.user_id', '=', 'users.id')
            ->where('users.id', auth()->user()->id)
            ->first();

        return view('backend.dropshipper.user.wallets_verify', compact('profile'));
    }

    public function TransactionHistory()
    {
        $transaction = Wallet::select(
            'wallets.id',
            'users.id as user_id',
            'wallets.user_id',
            'wallets.transaction_date',
            'wallets.transaction_balance',
            'wallets.mobile_no',
            'wallets.payment_type',
            'wallets.transaction_status',
            'users.name'
        )
            ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
            ->where('users.id', auth()->user()->id)
            ->get();

        return view('backend.dropshipper.user.transaction', compact('transaction'));
    }

    public function add_to_my_shop(Request $request, $product_id)
    {
        $exists = MyShop::where('user_id', Auth::id())->where('product_id', $product_id)->first();
        if (!$exists) {
            MyShop::insert([
                'user_id' => Auth::id(),
                'product_id' => $product_id,
                'created_at' => Carbon::now(),
            ]);
            return response()->json(['success' => 'Successfully Added To Your Product']);
        } else {
            return response()->json(['error' => 'This Product Already Exists In Your List']);
        }
    }

    public function myProductList(Request $request)
    {

        if (!$request->ajax()) {
            return view('backend.dropshipper.product.dropshipper_product');
        }
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];

        $Result = MyShop::getResult($start, $length, $columns);
        $sn = $start + 1;

        foreach ($Result as $key => $Res) {
            $img = $Res->product
                ? "<img style='width:50px; height:30px' src='" . asset('upload/product_images/' . $Res->product->image) . "'>"
                : 'N/A';

            $ShowRoute = route('dropshipper.product.view', $Res->id);
            $DeleteRoute = route('dropshipper.product.delete', $Res->id);
            $action = "<a title='Show' class='btn btn-sm btn-info' href='$ShowRoute'><i class='fas fa-eye'></i></a>";
            $action .= "<a title='Delete' class='btn btn-sm btn-danger' href='$DeleteRoute'><i class='fas fa-trash'></i></a>";

            $Data[] = [
                'sn' => $sn,
                'name' => $Res->product ? $Res->product->name : 'N/A',
                'image' => $img,
                'action' => $action
            ];
            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => MyShop::count(),
            "iTotalDisplayRecords" => MyShop::countResult($columns),
            "aaData" => $Data
        ];

        return response()->json($res);
    }

    public function productView($id)
    {

        $data['myshop'] = MyShop::with('product')->find($id);
        return view('backend.dropshipper.product.dropshipper_product_view', $data);
    }
    public function myProductDelete($id)
    {
        $data = MyShop::find($id);
        $data->delete();
        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function vendorProduct()
    {
        return view('backend.dropshipper.product.vendor_product');
    }

    public function vendorProductView()
    {
        return view('backend.dropshipper.product.vendor_product');
    }

    public function vendorProductList(Request $request)
    {
        if (!$request->ajax()) {
            return view('backend.dropshipper.product.vendor_product');
        }

        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];

        $Result = Product::getvendorResult($start, $length, $columns);
        $sn = $start + 1;

        foreach ($Result as $key => $Res) {
            $img = "<img style='width:50px; height:30px' src='" . url('upload/product_images/' . $Res->image) . "'>";

            $EditRoute = route('dropshipper.vendor.product.edit', $Res->id);
            $action = " <a title='Edit' class='btn btn-sm btn-primary' href='$EditRoute'><i class='fas fa-edit'></i></a>";

            if ($Res->discount_type == NULL) {
                $discount = '0 %';
            } elseif ($Res->discount_type == 1) {
                $discount = ($Res->discount . ' %');
            } else {
                $discount = ($Res->discount . ' Tk.');
            }

            $disValue = $Res->discount_type == 1
                ? $Res->price - ($Res->price * $Res->discount) / 100
                : $Res->price - $Res->discount;

            $Data[] = [
                'sn' => $sn,
                'category_id' => $Res->category['name'],
                'brand_name' => $Res->brand['name'],
                'name' => $Res->name,
                'sku' => $Res->sku ?? 'N/A',
                'tp' => $Res->trade_price,
                'price' => $Res->price,
                'discount' => $discount,
                'disValue' => $disValue,
                'image' => $img,
                'action' => $action,
                'id' => $Res->id,
            ];
            $sn++;
        }

        $res = [
            "draw" => $draw,
            "iTotalRecords" => Product::count(),
            "iTotalDisplayRecords" => Product::countResult($columns),
            "aaData" => $Data
        ];

        return response()->json($res);
    }

    public function addVendorProduct()
    {
        $data['categories'] = Category::all();
        $data['subcategories'] = Subcategory::all();
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sizes'] = Size::all();
        $data['color_array'] = $data['colors']->pluck('id')->toArray();
        $data['size_array'] = $data['sizes']->pluck('id')->toArray();
        $data['countries'] = Country::all();
        return view('backend.dropshipper.product.add_vendor_product', $data);
    }

    public function storeVendorProduct(Request $request)
    {
        DB::transaction(function () use ($request) {
            $this->validate($request, [
                'name' => 'required',
                'color_id' => 'required',
                'size_id' => 'required'
            ]);

            $value = $request->name;
            $p_name = str_replace('"', '', $value);
            $data = new Product();
            $data->category_id = $request->category_id;
            $data->subcategory_id = $request->subcategory_id;
            $data->brand_id = $request->brand_id;
            $data->user_id = Auth::user()->id;
            $data->name = $p_name;
            $data->slug = Str::slug($p_name);
            $data->price = $request->price;
            $data->discount_type = $request->discount_type;
            $data->discount = $request->discount;
            $data->status = $request->status;
            $img = $request->file('image');
            if ($img) {
                $imgName = date('YmdHi') . $img->getClientOriginalName();
                $img->move('upload/product_images/', $imgName);
                $data['image'] = $imgName;
            }
            $data->save();
        });
        return redirect()->route('dropshipper.vendor.product.view')->with('success', 'Product added successfully!');
    }

    public function editVendorProduct($id)
    {
        $data['editData'] = Product::find($id);
        $data['categories'] = Category::all();
        $data['subcategories'] = Subcategory::all();
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sizes'] = Size::all();
        $data['countries'] = Country::all();
        return view('backend.dropshipper.product.add_vendor_product', $data);
    }

    public function VendorProductUpdate(ProductRequest $request, $id)
    {
        $data = Product::find($id);
        $data->update($request->all());
        return redirect()->route('dropshipper.vendor.product.view')->with('success', 'Product updated successfully!');
    }

    public function deleteVendorProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if (file_exists('upload/product_images/' . $product->image) && !empty($product->image)) {
            unlink('upload/product_images/' . $product->image);
        }
        $product->delete();
        return redirect()->route('dropshipper.vendor.product.view')->with('success', 'Product deleted successfully!');
    }
}
