<?php

namespace App\Http\Controllers\Dropshipper;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Utilities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Refer List - Users referred by the dropshipper
     */
    public function refer_list()
    {
        $pageTitle = 'Refer List';
        $user = Auth::user();

        $data = User::where('dropshipper_id', $user->id)
            ->where('status', 1)
            ->get();

        return view('backend.dropshipper.reports.refer_list', compact('data', 'pageTitle'));
    }

    /**
     * Refer Commission List - Commissions from referrals
     */
    public function refer_commission_list()
    {
        $pageTitle = 'Refer Commissions';
        $user = Auth::user();

        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])
            ->where('tnx_type', Constant::TRANSACTION_TYPE['refer_commission'])
            ->where('user_id', $user->id)
            ->get();

        return view('backend.dropshipper.reports.refer_commissions', compact('data', 'pageTitle'));
    }

    /**
     * Sales List - Product sales made by dropshipper
     */
    public function refer_sales_list()
    {
        $pageTitle = 'Product Sales';
        $user = Auth::user();

        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])
            ->where('tnx_type', Constant::TRANSACTION_TYPE['product_sales'])
            ->where('user_id', $user->id)
            ->get();

        return view('backend.dropshipper.reports.dropshipper_sales_reports', compact('data', 'pageTitle'));
    }

    /**
     * Dropshipper Commission Reports - Commissions from sales
     */
    public function reseller_commission_reports()
    {
        $pageTitle = 'Dropshipper Sales Commissions';
        $user = Auth::user();

        $data = Transaction::where('wallet_type', Constant::WALLET_TYPE['balance_wallet'])
            ->where('tnx_type', Constant::TRANSACTION_TYPE['dropshipper_sales_commission'])
            ->where('user_id', $user->id)
            ->get();

        return view('backend.dropshipper.reports.reseller_commission_reports', compact('data', 'pageTitle'));
    }
}
