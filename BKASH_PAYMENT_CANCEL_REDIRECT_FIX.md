# BKash Payment Cancel/Failure Redirect Fix

## Problem Description
When users (Customer, Seller, Vendor, or Dropshipper) clicked the "Cancel" button on the BKash payment page without making a payment, they were being redirected to `localhost:8000/login` or `https://usupershop.com/login` (admin login page) instead of their respective account home pages.

## Root Cause Analysis
The issue was caused by incorrect redirect logic in multiple places:

1. **Frontend BkashPaymentGatewayController.php** - The callback method was redirecting to `route('dashboard')` which requires authentication with 'customer' middleware
2. **API BkashPaymentGatewayController.php** - The actual callback handler that processes BKash payments via `/api/callback/bkash/`
3. **cancel.blade.php** - Was redirecting to `route('dashboard')` without proper user type checking
4. **failed.blade.php** - Had the same issue as cancel.blade.php
5. **sucess.blade.php** - Also had similar redirect issues

Since `route('dashboard')` is protected by authentication middleware and requires 'customer' user type, unauthenticated users or users with different user types were being redirected to the login page.

**Key Discovery:** The actual BKash callback is handled by the API route `/api/callback/bkash/` which uses `App\Http\Controllers\Api\BkashPaymentGatewayController::bkashcallback_new()`, not the Frontend controller.

## Solution Implemented

### Files Modified:

#### 1. `app/Http/Controllers/Frontend/BkashPaymentGatewayController.php`
**Changes:**
- Updated the cancel/failure redirect logic to check user authentication status
- Added proper user type checking (customer, seller, vendor, dropshipper)
- Changed fallback redirect from `route('dashboard')` to `route('frontend.home')` for unauthenticated users
- Now redirects users to their appropriate dashboards based on user type:
  - **Customer** → `route('dashboard')`
  - **Seller/Vendor** → `route('seller.dashboard')`
  - **Dropshipper** → `route('dropshipper.dashboard')`
  - **Unauthenticated** → `route('frontend.home')`

#### 2. `app/Http/Controllers/Api/BkashPaymentGatewayController.php` ⭐ **CRITICAL FIX**
**Changes:**
- Modified the cancel/failure section to fetch user information from the order/subscription
- Added user data to `$returnData['user']` before passing to views
- This ensures the views have access to user information for proper redirect logic
- Added same logic for success payments to ensure consistent behavior

**Key Implementation:**
```php
// Get user information for redirect
$user = null;
if ($paymentType === 'customer_order') {
    $order_id = explode('-', $paymentData['merchantInvoiceNumber'])[0];
    $order = Order::find($order_id);
    if ($order) {
        $user = User::find($order->user_id);
        auth()->loginUsingId($order->user_id);
    }
    // ... handle payment confirmation
} elseif ($paymentType === 'user_subscription') {
    $subscription_id = explode('-', $paymentData['merchantInvoiceNumber'])[0];
    $subscription = SubscriptionFee::find($subscription_id);
    if ($subscription) {
        $user = User::find($subscription->seller_id);
        auth()->loginUsingId($subscription->seller_id);
    }
}

$returnData['user'] = $user;
```

#### 3. `resources/views/frontend/cancel.blade.php`
**Changes:**
- Changed from using `@auth` directive to directly checking `$returnData['user']`
- This is more reliable since the user data is explicitly passed from the controller
- Simplified the redirect logic by extracting user type from the passed user object

**New Redirect Logic:**
```php
@if (isset($returnData['user']) && $returnData['user'] != NULL)
  @php
    $userType = $returnData['user']->usertype ?? null;
  @endphp
  @if ($userType === 'customer')
    window.location.href = "{{ route('dashboard') }}";
  @elseif ($userType === 'seller' || $userType === 'vendor')
    window.location.href = "{{ route('seller.dashboard') }}";
  @elseif ($userType === 'dropshipper')
    window.location.href = "{{ route('dropshipper.dashboard') }}";
  @else
    window.location.href = "{{ route('frontend.home') }}";
  @endif
@else
  window.location.href = "{{ route('frontend.home') }}";
@endif
```

#### 4. `resources/views/frontend/failed.blade.php`
**Changes:**
- Applied the same user data checking approach as cancel.blade.php
- Ensures failed payments redirect users to their appropriate home pages
- Prevents unauthenticated users from being redirected to login page

#### 5. `resources/views/frontend/sucess.blade.php`
**Changes:**
- Updated success redirect logic to use passed user data
- For customer orders:
  - **Customer** → `route('customer.order.list')`
  - **Seller/Vendor/Dropshipper** → `route('seller.customer.order.list')`
- For subscription payments:
  - **Seller/Vendor** → `route('seller.dashboard')`
  - **Dropshipper** → `route('dropshipper.dashboard')`
- Added fallback to `route('frontend.home')` for unauthenticated users

## Why This Fix Works

1. **Direct User Data:** Instead of relying on Laravel's auth session (which might not persist properly in API callbacks), we now pass the user object directly to the view
2. **Explicit User Type Check:** The view now checks the user type from the passed data, not from the session
3. **Fallback to Home:** If no user data is available, it redirects to the home page instead of trying to access protected routes
4. **Consistent Across All Payment States:** Success, cancel, and failure all use the same logic
5. **API Route Handling:** Fixed the actual callback route that BKash uses (`/api/callback/bkash/`)

## Redirect Logic Summary

### Payment Cancel/Failure:
```
If User Data Available:
  - Customer → Customer Dashboard (route('dashboard'))
  - Seller/Vendor → Seller Dashboard (route('seller.dashboard'))
  - Dropshipper → Dropshipper Dashboard (route('dropshipper.dashboard'))
Else:
  - Redirect to Home Page (route('frontend.home'))
```

### Payment Success:
```
If Customer Order:
  If User Data Available:
    - Customer → Customer Order List (route('customer.order.list'))
    - Seller/Vendor/Dropshipper → Seller Customer Order List (route('seller.customer.order.list'))
  Else:
    - Redirect to Home Page

If Subscription Payment:
  If User Data Available:
    - Seller/Vendor → Seller Dashboard (route('seller.dashboard'))
    - Dropshipper → Dropshipper Dashboard (route('dropshipper.dashboard'))
  Else:
    - Redirect to Home Page
```

## Testing Recommendations

Test the following scenarios:

1. **Customer Account:**
   - Place an order and cancel payment → Should redirect to customer dashboard
   - Place an order and complete payment → Should redirect to customer order list

2. **Seller Account:**
   - Place an order and cancel payment → Should redirect to seller dashboard
   - Place an order and complete payment → Should redirect to seller customer order list
   - Pay subscription fee and cancel → Should redirect to seller dashboard
   - Pay subscription fee and complete → Should redirect to seller dashboard

3. **Dropshipper Account:**
   - Place an order and cancel payment → Should redirect to dropshipper dashboard
   - Place an order and complete payment → Should redirect to seller customer order list
   - Pay subscription fee and cancel → Should redirect to dropshipper dashboard
   - Pay subscription fee and complete → Should redirect to dropshipper dashboard

4. **Vendor Account:**
   - Place an order and cancel payment → Should redirect to seller dashboard
   - Place an order and complete payment → Should redirect to seller customer order list

5. **Unauthenticated/Guest User:**
   - Any payment cancel/failure → Should redirect to home page (not login page)

## Deployment Instructions

After deploying these changes to the live server:

1. Clear all caches:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   php artisan route:clear
   ```

2. Test with each user type (Customer, Seller, Vendor, Dropshipper)

3. Verify that canceling payment redirects to the appropriate dashboard, not the login page

## Benefits

1. **Improved User Experience:** Users are now redirected to their appropriate home pages instead of the login page
2. **Better Navigation:** Each user type gets redirected to their relevant dashboard/page
3. **No More Login Loop:** Unauthenticated users won't get stuck in a login redirect loop
4. **Consistent Behavior:** All payment status pages (success, cancel, failed) now have consistent redirect logic
5. **Error Prevention:** Added proper checks to prevent undefined variable errors
6. **API Route Fixed:** The actual BKash callback route now properly handles user redirects

## Date: 2025
## Status: ✅ FIXED (Updated with API Controller Fix)
