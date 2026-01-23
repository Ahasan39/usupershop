<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Logo;
use App\Models\Product;
use App\Models\SellerEmail;
use App\Models\Slider;
use App\Models\User;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt; 

class SellerController extends Controller
{
    use SendSmsTrait;
    public function sellerLogin()
    {
        if(auth()->user()){
            if(auth()->user()->usertype === 'customer'){
                return redirect()->route('kafi');
            }
            if(auth()->user()->usertype === 'vendor'){
                return redirect()->route('seller.dashboard');
            }
            if(auth()->user()->usertype === 'seller'){
                return redirect()->route('seller.dashboard');
            }
            if(auth()->user()->usertype === 'dropshipper'){
                return redirect()->route('seller.dashboard');
            }
        }
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        return view('frontend.seller.seller-login', $data);
    }

    public function sellerSignup()
    {
        if(auth()->user()){
            if(auth()->user()->usertype === 'customer'){
                return redirect()->route('kafi');
            }
            if(auth()->user()->usertype === 'vendor'){
                return redirect()->route('seller.dashboard');
            }
            if(auth()->user()->usertype === 'seller'){
                return redirect()->route('seller.dashboard');
            }
        }
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        return view('frontend.seller.seller-signup', $data);
    }

    public function sellerStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'account_type' => 'required',
            'subscription_plan' => 'required',
            'shop_name' => 'required',
            'email' => 'required|unique:users,email',
            'mobile' => ['required', 'unique:users,mobile', 'regex:/(^(\+8801|8801|01|008801))[1|5-9]{1}(\d){8}$/'],
            'password' => 'min:9|required_with:confirmation_password|same:confirmation_password',
            'confirmation_password' => 'min:9',
            'address' => 'required',
            'terms' => 'required',
            'refer_code' => ['nullable', 'exists:users,refer_code']
        ]);

        try {

            DB::beginTransaction();

            do {
                $refer_code = Str::upper(Str::random(8));
            } while (User::where('refer_code', $refer_code)->exists());

            $otp = rand(100000, 999999);
            $user = new User();
            $user->refer_code = $refer_code;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->subscription_plan = strtotime($request->subscription_plan);
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->code = $otp;
            $user->status = 0;  // 0 means inactive and 1 means active and 2 means suspended
            $user->payment_status = 0; // 0 unpaid and 1 paid
            $user->account_type = $request->account_type;
            $user->shop_name = $request->shop_name;
            $user->terms = $request->terms;
            $user->usertype = $user->account_type;
            $user->address = $request->address;
            $user->email_verified_at = NULL;

            // Find reseller/vendor user by the provided code
            $reseller = User::where('refer_code', $request->code)->whereIn('usertype', ['seller', 'vendor'])->first();
            if ($reseller) {
                $reseller->is_reseller = 1;
                if ($reseller->payment_status == 2) {
                    if ($reseller->is_profile_verify == 1) {
                        $user->reseller_id = $reseller->id;
                    }
                }
                $reseller->updated_at = now();
                $reseller->save();
            }

            $user->save();

            Session::put('seller_verify', $request->mobile);

            SellerEmail::updateOrCreate(['seller_email' => $user->email], ['code' => $refer_code]);


            $mobile = '88' . $user->mobile;
            $smsMessage = "Hello Dear $user->name, Your U SuperShop verification code is: $otp. The code will expire in 5 minutes. Please do NOT share your OTP or PIN with others.";
            $this->send_rapid_message($mobile, $smsMessage);

            Mail::send('frontend.emails.verify-email', ['email' => $user->email, 'code' => $otp], function ($message) use ($user) {
                $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
                $message->to($user->email);
                $message->subject(env('APP_NAME').' | OTP Verification');
            });


            DB::commit();

            $encrypted_id = Crypt::encrypt($user->id);
            return redirect()->route('verify.index', $encrypted_id)->with('success', 'You have successfully signed up, Please verify your OTP');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function sellerAccountVerifyCode()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        return view('frontend.seller.seller-email-verify', $data);
    }
    public function sellerForgetPassword()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        return view('frontend.seller.forget_password', $data);
    }
    public function sellerOtp()
    {
        $data['logo'] = Logo::first();
        $data['contact'] = Contact::first();
        return view('frontend.seller.send-seller-otp', $data);
    }
    public function sellerEmailVerifyCodeSave(Request $request)
    {
        $code = rand(000000, 999999);
        $user = User::where('email', $request->content)->orWhere('mobile', $request->content)->first();
        if (!$user) {
            return back()->with('error', 'User not found with provided email or mobile.');
        }
        Session::put('seller_verify', $request->content);
        // Send SMS if verifying via mobile
        if ($request->content == $user->mobile) {
            $mobile = '88' . $user->mobile;
            $smsMessage = "Hello Dear $user->name, Your U SuperShop verification code is: $code. The code will expire in 5 minutes. Please do not share your OTP or PIN with others";
            $smsResponse = $this->send_rapid_message($mobile, $smsMessage);
            if (!isset($smsResponse['status']) || $smsResponse['status'] !== 'success') {
                Log::error('SMS sending failed', ['response' => $smsResponse]);
            }
        }
        // Update user status
        $user->status = 1;
        $user->code = $code;
        $user->email_verified_at = Carbon::now();
        $user->save();
        return redirect()->route('seller_email.get')->with('success', 'You have successfully verified. Please login now.');
    }

    public function sellerVerifyStore(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'code' => 'required',
        ]);
        // Find user by email or mobile
        $checkData = User::where('email', $request->content)->orWhere('mobile', $request->content)->first();

        // Check if user exists and code matches
        if ($checkData && $checkData->code === $request->code) {
            $checkData->email_verified_at = Carbon::now();
            $checkData->update();
            DB::table('password_resets')->insert([
                'email' => $checkData->email,
                'mobile' => $checkData->mobile,
                'otp' => $checkData->code,
                'created_at' => Carbon::now(),
            ]);
            return redirect()->route('seller.forget_password')->with('success', 'You have successfully verified');
        } else {
            return redirect()->back()->with('error', 'Sorry! Email or verification code does not match.');
        }
    }

    public function seller_forget_passwordSave(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Find the user by email or mobile
        $user = User::where('email', $request->content)->orWhere('mobile', $request->content)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        Session::forget('seller_verify');
        // Delete password reset record
        DB::table('password_resets')->where('email', $request->content)->orWhere('mobile', $request->content)->delete();

        return redirect()->route('seller.login')->with('success', 'Password reset successful. Please login now!');
    }

    public function sellerLoginSave(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'account_type' => 'required',
            'text_content' => 'required',
            'password' => 'required',
        ]);
        $emialcredentials = ['email' => $request->text_content, 'password' => $request->password];
        $mobilecredentials = ['mobile' => $request->text_content, 'password' => $request->password];
        // Attempt login based on input type
        if (Auth::attempt($emialcredentials)) {
            $user = Auth::user();

            $expirationDate = $user->subscription_plan;
            $startDate = $expirationDate;
            $endDate = time();
            // Calculate the difference in seconds, then convert to days
            $differenceInDays = abs(($endDate - $startDate) / (60 * 60 * 24));
            if ($differenceInDays <= 365) {
                // Redirect to seller dashboard if login is successful and subscription is valid
                return redirect()->route('seller.dashboard')->with('success', 'Login successful');
            } else {
                return redirect()->back()->with('success', 'Your Subscription plan is expired');
            }
        } elseif (Auth::attempt($mobilecredentials)) {
            $user = Auth::user();
            $expirationDate = $user->subscription_plan;
            $startDate = $expirationDate;
            $endDate = time();
            // Calculate the difference in seconds, then convert to days
            $differenceInDays = abs(($endDate - $startDate) / (60 * 60 * 24));
            if ($differenceInDays <= 365) {
                // Redirect to seller dashboard if login is successful and subscription is valid
                return redirect()->route('seller.dashboard')->with('success', 'Login successful');
            } else {
                return redirect()->back()->with('success', 'Your Subscription plan is expired');
            }
        } else {
            // Redirect with an error message for invalid credentials
            return redirect()->back()->with('message', 'Invalid email/mobile number or password.');
        }
    }
}
