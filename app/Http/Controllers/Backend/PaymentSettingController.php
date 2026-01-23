<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use Auth;

class PaymentSettingController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $payment = PaymentSetting::where('user_id', auth()->user()->id)
                ->where('method', strtolower(request()->method_type))
                ->first();

            return response()->json([
                'status' => true,
                'data' => $payment,
            ]);
        }

        $payment = PaymentSetting::where('user_id', auth()->user()->id)->first();

        if (auth()->user()->usertype == 'dropshipper') {
            return view('backend.dropshipper.user.payment_setting', compact('payment'));
        } else if (auth()->user()->usertype == 'seller') {
            return view('backend.seller.user.payment_setting', compact('payment'));
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'method' => 'required',
            'number' => 'required|digits_between:10,15',
        ]);

        PaymentSetting::updateOrCreate(
            [
                'user_id' => auth()->user()->id,
                'method' => $request->method,
            ],
            [
                'number' => $request->number,
            ]
        );

        return back()->with('success', 'Payment information saved successfully!');
    }

    public function dropshipperIndex()
    {
        if (request()->ajax()) {
            $payment = PaymentSetting::where('user_id', auth()->user()->id)
                ->where('method', strtolower(request()->method_type))
                ->first();

            return response()->json([
                'status' => true,
                'data' => $payment,
            ]);
        }

        $payment = PaymentSetting::where('user_id', auth()->user()->id)->first();

        if (auth()->user()->usertype == 'dropshipper') {
            return view('backend.dropshipper.user.payment_setting', compact('payment'));
        } else if (auth()->user()->usertype == 'seller') {
            return view('backend.seller.user.payment_setting', compact('payment'));
        }
    }

    public function dropshipperStore(Request $request)
    {
        $request->validate([
            'method' => 'required',
            'number' => 'required|digits_between:10,15',
        ]);

        PaymentSetting::updateOrCreate(
            [
                'user_id' => auth()->user()->id,
                'method' => $request->method,
            ],
            [
                'number' => $request->number,
            ]
        );

        return back()->with('success', 'Payment information saved successfully!');
    }
}

