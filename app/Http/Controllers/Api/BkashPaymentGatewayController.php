<?php

namespace App\Http\Controllers\Api;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\SubscriptionFee;
use App\Traits\BkashPaymentTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shipping;
use App\Traits\ReferCommissionTrait;
use App\utilities\Constant;
use Illuminate\Support\Facades\Session;

class BkashPaymentGatewayController extends Controller
{
    use BkashPaymentTrait, ReferCommissionTrait;


    public function bkashcallback_new()
    {
        $status = request()->get('status');
        $paymentID = request()->get('paymentID');
        $paymentType = request()->get('payment_type');

        $returnData = ['payment_type' => $paymentType];

        if (!$status || !$paymentID || !$paymentType) {
            return view('frontend.failed', compact('returnData'));
        }

        $grant = $this->grantToken();
        if (!isset($grant['id_token'])) {
            return view('frontend.failed', compact('returnData'));
        }

        $token = $grant['id_token'];

        // SUCCESS
        if ($status === 'success') {

            $execute = $this->paymentExecute($paymentID, $token);

            if (
                !isset($execute['data']) ||
                $execute['data']['statusCode'] !== '0000' ||
                $execute['data']['transactionStatus'] !== 'Completed'
            ) {

                return view('frontend.failed', compact('returnData'));
            }

            $paymentData = [
                'trxID' => $execute['data']['trxID'],
                'merchantInvoiceNumber' => $execute['data']['merchantInvoiceNumber'],
                'amount' => $execute['data']['amount'] ?? 0,
            ];

            if ($paymentType === 'customer_order') {

                if ($this->customer_payment_confirmation('success', $paymentData)) {
                    return view('frontend.sucess', compact('returnData'));
                }

                return view('frontend.failed', compact('returnData'));
            }

            if ($paymentType === 'user_subscription') {

                if ($this->user_subscription_payment_confirmation('success', $paymentData)) {
                    return view('frontend.sucess', compact('returnData'));
                }

                return view('frontend.failed', compact('returnData'));
            }

            return view('frontend.failed', compact('returnData'));
        }


        // FAILURE OR CANCEL
        $check = $this->checkPayment($paymentID, $token);

        if (!isset($check['status']) || !$check['status']) {
            return view('frontend.failed', compact('returnData'));
        }

        $paymentData = [
            'trxID' => '',
            'merchantInvoiceNumber' => $check['data']['merchantInvoice'],
            'amount' => 0,
        ];

        if ($paymentType === 'customer_order') {

            $this->customer_payment_confirmation(
                $status === 'failure' ? 'fail' : 'cancel',
                $paymentData
            );
        }

        return $status === 'failure'
            ? view('frontend.failed', compact('returnData'))
            : view('frontend.cancel', compact('returnData'));
    }


    private function customer_payment_confirmation($payment_status, $data)
    {
        $order_id = explode('-', $data['merchantInvoiceNumber'])[0];
        $order = Order::find($order_id);

        if (!$order)
            return false;

        auth()->loginUsingId($order->user_id);

        try {
            DB::beginTransaction();

            if ($payment_status === 'success') {

                DB::table('payments_transaction')->insert([
                    'client_id' => $order->user_id,
                    'order_id' => $order->id,
                    'transaction_type' => 'bkash_payment',
                    'trxID' => $data['trxID'],
                    'payment_method' => 'bkash',
                    'credit' => $data['amount'],
                    'debit' => 0,
                    'order_note' => 'order bkash payment',
                    'status' => 0
                ]);

                $order->update([
                    'status' => 'confirmed',
                    'order_payment' => 'Paid'
                ]);

                DB::commit();
                return true;
            }


            if ($payment_status === 'fail') {

                $items = OrderDetail::where('order_id', $order_id)->get();

                foreach ($items as $item) {
                    Product::where('id', $item->product_id)->increment('quantity', $item->quantity);
                }

                Shipping::destroy($order->shipping_id);
                Payment::destroy($order->payment_id);

                OrderDetail::where('order_id', $order_id)->delete();

                $order->delete();

                DB::commit();
                return true;
            }


            if ($payment_status === 'cancel') {

                OrderDetail::where('order_id', $order_id)->get()
                    ->each(
                        fn($item) =>
                        Product::where('id', $item->product_id)->increment('quantity', $item->quantity)
                    );

                $order->update([
                    'order_payment' => 'Unpaid',
                    'status' => 'canceled'
                ]);

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    private function user_subscription_payment_confirmation($payment_status, $data)
    {
        $subscription_id = explode('-', $data['merchantInvoiceNumber'])[0];

        $subscription = SubscriptionFee::find($subscription_id);

        if (!$subscription)
            return false;

        auth()->loginUsingId($subscription->seller_id);

        try {

            DB::beginTransaction();

            if ($payment_status === 'success') {

                $user = User::find($subscription->seller_id);

                $user->payment_status = 1;
                if ($user->usertype === 'seller') {
                    $user->commission = Constant::COMMISSION['seller_comission'];
                }
                $user->save();

                $subscription->update([
                    'date' => time(),
                    'status' => 1,
                ]);

                if ($user->reseller_id) {
                    $this->distribute_refer_commission($user, $data['amount']);
                }

                DB::commit();
                return true;
            }

            // fail OR cancel

            User::where('id', $subscription->seller_id)
                ->update(['payment_status' => 0]);

            $subscription->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

}
