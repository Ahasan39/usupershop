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


class SellerProductController extends Controller
{
    public function shopkeeper_product(){
        $data['products']= Product::all();
        return view('backend.seller.product.shopkeeper_product', $data);
    }
    public function SearchProducts(Request $request){
        $keyword =  $request->product_search;
        $data['products'] = Product::where('name', 'LIKE', '%' . $keyword . '%')
        ->orWhere('sku', 'LIKE', '%' . $keyword . '%')
        ->orWhere('short_desc', 'LIKE', '%' . $keyword . '%')
        ->get();
        return view('backend.seller.product.shopkeeper_product', $data);
    }
    public function MyWallets(){
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
     
        return view('backend.seller.user.wallets',compact('profile'));
    }
    public function MyWalletsVerified(){
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
     
        return view('backend.seller.user.wallets_verify',compact('profile'));
    }
    public function TransactionHistory(){
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
        return view('backend.seller.user.transaction',compact('transaction'));
    }
    public function add_to_my_shop( Request $request, $product_id){
        $exists = MyShop::where('user_id',Auth::id())->where('product_id',$product_id)->first();
        if (!$exists) {
            MyShop::insert([
                'user_id' => Auth::id(),
                'product_id' => $product_id,
                'created_at' => Carbon::now(),
            ]);
            return response()->json(['success' => 'Sucessfully Added On Your Product']);
        }else {
            return response()->json(['error' => 'The Product Has Already On Your Product']);
        }

    }

    public function seller_product(){
        return view('backend.seller.product.seller_product');
    }


    public function myproductlist(Request $request)
    {
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');
        $Data = [];
        $Result = MyShop::getResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {
            if($Res->product){
                $img = "<img style='width:50px; height:30px' src='" . asset('upload/product_images/' . $Res->product->image) . "'>";
            }
            else{
                $img = 'N/A';
            }
            $DeleteRoute = route('sellers.products.delete',$Res->id);
            $action = "<a title='Delete' class='btn btn-sm btn-danger' href='$DeleteRoute'><i class='fas fa-trash'></i></a>";
            $Data[] = array(
                'sn' => $sn,
                'name' => $Res->product ? $Res->product->name : 'N/A',
                'image' => $img,
                'action' => $action
            );
            $sn++;
        }
        $res = array(
            "draw" => $draw,
            "iTotalRecords" => MyShop::count(),
            "iTotalDisplayRecords" => MyShop::countResult($columns),
            "aaData" => $Data
        );
        return response()->json($res);
    }

    public function myProductDelete($id){
        $data = MyShop::find($id);
        $data->delete();
        return redirect()->back()->with('success', 'Data deleted successfully !!!');
    } 
    public function vendorProductView(){
        return view('backend.seller.product.vendor_product');
    }
    public function vendorProductList(Request $request){
        $draw = $request->input("draw");
        $length = $request->input("length");
        $start = $request->input("start");
        $columns = $request->input('columns');

        $Data = [];
        $Result = Product::getvendorResult($start, $length, $columns);
        $sn = $start + 1;
        foreach ($Result as $key => $Res) {

            $img = "<img style='width:50px; height:30px' src='" . url('upload/product_images/' . $Res->image) . "'>";

           // $ShowRoute = route('products.show', $Res->id);
            $EditRoute = route('vendor.editproduct', $Res->id);
          //  $DeleteRoute = route('vendor.deleteproduct', $Res->id);

           // $action = "<a title='Show' class='btn btn-sm btn-info' href='$ShowRoute'><i class='fas fa-eye'></i></a>";
            $action = " <a title='Edit' class='btn btn-sm btn-primary' href='$EditRoute'><i class='fas fa-edit'></i></a>";
         //   $action .= " <a title='Delete' class='btn btn-sm btn-danger' href='$DeleteRoute'><i class='fas fa-trash'></i></a>";

            if ($Res->discount_type == NULL) {
                $discount = '0 %';
            } elseif ($Res->discount_type == 1) {
                $discount = ($Res->discount . ' %');
            } else {
                $discount = ($Res->discount . ' Tk.');
            }

            if ($Res->discount_type == 1) {
                $disValue = $Res->price - ($Res->price * $Res->discount) / 100;
            } else {
                $disValue = ($Res->price) - ($Res->discount);
            }

            $Data[] = array(
                'sn' => $sn,
                'category_id' => $Res->category['name'],
                'brand_name' => $Res->brand['name'],
                'name' => $Res->name,
                'sku' => $Res->sku ?? '123456',
                'tp' => $Res->trade_price,
                'price' => $Res->price,
                'discount' => $discount,
                'disValue' => $disValue,
                'image' => $img,
                'action' => $action,
                'id' => $Res->id,
            );

            $sn++;
        }

        $res = array(
            "draw" => $draw,
            "iTotalRecords" => Product::count(),
            "iTotalDisplayRecords" => Product::countResult($columns),
            "aaData" => $Data
        );

        return response()->json($res);
    }
    public function addVendorProduct(){
        $data['categories'] = Category::all();
        $data['subcategories'] = Subcategory::all();
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sizes'] = Size::all();
        $data['color_array'] = $data['colors']->pluck('id')->toArray(); 
        $data['size_array'] = $data['sizes']->pluck('id')->toArray(); 
        $data['countries'] = Country::all();
        return view('backend.seller.product.add_vendor_product', $data);
    }
    public function storeVendorProduct(Request $request){
    
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
            $data->name_bn = $request->name_bn;
            $data->sku = $request->sku;
            $data->slug = Str::slug($p_name);
            $data->country_id = $request->country_id;
            $data->short_desc = $request->short_desc;
            $data->short_desc_bn = $request->short_desc_bn;
            $data->long_desc = $request->long_desc;
            $data->trade_price = $request->trade_price;
            $data->price = $request->price;
            $data->discount_type = $request->discount_type;
            $data->discount = $request->discount;
            $data->hot_deals =$request->hot_deals;
            $data->featured =$request->featured;
            $data->special_offer = $request->special_offer;
            $data->special_deals = $request->special_deals;
            $data->quantity = $request->quantity;
            $data->status = $request->status;
            $img = $request->file('image');
            if ($img) {
                $imgName = date('YmdHi') . $img->getClientOriginalName();
                $img->move('upload/product_images/', $imgName);
                $data['image'] = $imgName;
            }
          
            if ($data->save()) {
                //Product sub image table data insert
                $files = $request->sub_image;
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $imgName = date('YmdHi') . $file->getClientOriginalName();
                        $file->move('upload/product_images/product_sub_images', $imgName);
                        $subimage['sub_image'] = $imgName;

                        $subimage = new ProductSubImage();
                        $subimage->product_id = $data->id;
                        $subimage->sub_image = $imgName;
                        $subimage->save();
                    }
                }
                //Product color table data insert
                $colors = $request->color_id ?? 0;
                if (!empty($colors)) {
                    foreach ($colors as $color) {
                        $mycolor = new ProductColor();
                        $mycolor->product_id = $data->id;
                        $mycolor->color_id = $color;
                        $mycolor->save();
                    }
                }

                //Product size table data insert
                $sizes = $request->size_id ?? 0;
                if (!empty($sizes)) {
                    foreach ($sizes as $size) {
                        $mysize = new ProductSize();
                        $mysize->product_id = $data->id;
                        $mysize->size_id = $size;
                        $mysize->save();
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Sorry! Data not save !!!');
            }
        });
        return redirect()->route('vendor.productview')->with('success', 'Data inserted successfully');
    }
    public function editVendorProduct($id){
        $data['editData'] = Product::find($id);
        $data['categories'] = Category::all();
        $data['subcategories'] = Subcategory::all();
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sizes'] = Size::all();
        $data['countries'] = Country::all();
        $data['color_array'] = ProductColor::select('color_id')->where('product_id', $data['editData']->id)->orderBy('id', 'desc')->get()->toArray();
        $data['size_array'] = ProductSize::select('size_id')->where('product_id', $data['editData']->id)->orderBy('id', 'desc')->get()->toArray();
        return view('backend.seller.product.add_vendor_product', $data);
    }
    public function VendorProductUpdate(ProductRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $this->validate($request, [
                'color_id' => 'required',
                'size_id' => 'required'
            ]);
            $data = Product::find($id);
            $data->category_id = $request->category_id;
            $data->subcategory_id = $request->subcategory_id;
            $data->brand_id = $request->brand_id;
            $data->user_id = Auth::user()->id;
            $data->name = $request->name;
            $data->sku = $request->sku;
            $data->name_bn = $request->name_bn;
            $data->slug = Str::slug($request->name);
            $data->country_id = $request->country_id;
            $data->short_desc = $request->short_desc;
            $data->short_desc_bn = $request->short_desc_bn;
            $data->long_desc = $request->long_desc;
            $data->trade_price = $request->trade_price;
            $data->price = $request->price;
            $data->discount_type = $request->discount_type;
            $data->discount = $request->discount;
            $data->status = 2;
            $data->hot_deals =$request->hot_deals;
            $data->featured =$request->featured;
            $data->special_offer = $request->special_offer;
            $data->special_deals = $request->special_deals;
            $img = $request->file('image');
            if ($img) {
                $imgName = date('YmdHi') . $img->getClientOriginalName();
                $img->move('upload/product_images/', $imgName);
                if (file_exists('upload/product_images/' . $data->image) && !empty($data->image)) {
                    unlink('upload/product_images/' . $data->image);
                }
                $data['image'] = $imgName;
            }
           
            if ($data->save()) {
                //Product sub image table data update
                $files = $request->sub_image;
                if (!empty($files)) {
                    $subImage = ProductSubImage::where('product_id', $id)->get()->toArray();
                    foreach ($subImage as $value) {
                        if (!empty($value)) {
                            unlink('upload/product_images/product_sub_images/' . $value['sub_image']);
                        }
                    }
                    ProductSubImage::where('product_id', $id)->delete();
                }
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $imgName = date('YmdHi') . $file->getClientOriginalName();
                        $file->move('upload/product_images/product_sub_images', $imgName);
                        $subimage['sub_image'] = $imgName;
                        $subimage = new ProductSubImage();
                        $subimage->product_id = $data->id;
                        $subimage->sub_image = $imgName;
                        $subimage->save();
                    }
                }
                //Product color table data update
                $colors = $request->color_id;
                if (!empty($colors)) {
                    ProductColor::where('product_id', $id)->delete();
                }
                if (!empty($colors)) {
                    foreach ($colors as $color) {
                        $mycolor = new ProductColor();
                        $mycolor->product_id = $data->id;
                        $mycolor->color_id = $color;
                        $mycolor->save();
                    }
                }
                //Product size table data update
                $sizes = $request->size_id;
                if (!empty($sizes)) {
                    ProductSize::where('product_id', $id)->delete();
                }
                if (!empty($sizes)) {
                    foreach ($sizes as $size) {
                        $mysize = new ProductSize();
                        $mysize->product_id = $data->id;
                        $mysize->size_id = $size;
                        $mysize->save();
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Sorry! Data not update !!!');
            }
        });
        return redirect()->route('vendor.productview')->with('success', 'Data updated successfully');
    }

    public function deleteVendorProduct(Request $request, $id)
    {

        $product = Product::find($id);
        if (file_exists('upload/product_images/' . $product->image) && !empty($product->image)) {
            unlink('upload/product_images/' . $product->image);
        }
        $subImage = ProductSubImage::where('product_id', $product->id)->get()->toArray();
        foreach ($subImage as $value) {
            if (!empty($value)) {
                unlink('upload/product_images/product_sub_images/' . $value['sub_image']);
            }
        }
        ProductSubImage::where('product_id', $product->id)->delete();
        ProductColor::where('product_id', $product->id)->delete();
        ProductSize::where('product_id', $product->id)->delete();
        $product->delete();
        return redirect()->route('vendor.productview')->with('success', 'Data deleted successfully !');
    }
}