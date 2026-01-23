<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\utilities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function refer_list(){
        $pageTitle = 'Refer List';
        $user = Auth::user();
        $data = User::where('reseller_id', $user->id)->where('status', 1)->get();

        return view('backend.seller.reports.refer_list', compact('data', 'pageTitle'));
    }
    public function refer_commission_list(){
        $pageTitle = 'Refer Commissions';
        $user = Auth::user();
        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])->where('tnx_type', Constant::TRANSACTION_TYPE['refer_commission'])->where('user_id', $user->id)->get();

        return view('backend.seller.reports.refer_commissions', compact('data', 'pageTitle'));
    }
    public function refer_sales_list(){
        $pageTitle = 'Product Sales';
        $user = Auth::user();
        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])->where('tnx_type', Constant::TRANSACTION_TYPE['product_seles'])->where('user_id', $user->id)->get();

        return view('backend.seller.reports.vendor_sales_reports', compact('data', 'pageTitle'));
    }
    public function reseller_commission_reports(){
        $pageTitle = 'Reseller Sales Commissions';
        $user = Auth::user();
        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])->where('tnx_type', Constant::TRANSACTION_TYPE['reseller_seles_commission'])->where('user_id', $user->id)->get();

        return view('backend.seller.reports.reseller_commission_reports', compact('data', 'pageTitle'));
    }
}
