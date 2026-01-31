# SSL Certificate Fix for Local Development

## The Problem
When testing payment gateways (bKash, EPS, etc.) on localhost, you may encounter:
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

## Quick Fix for Development (Choose ONE option)

### Option 1: Download CA Certificate Bundle (RECOMMENDED)
1. Download the latest CA bundle from: https://curl.se/ca/cacert.pem
2. Save it to: `C:\php\extras\ssl\cacert.pem` (or any path you prefer)
3. Update your `php.ini` file:
   ```ini
   curl.cainfo = "C:\php\extras\ssl\cacert.pem"
   openssl.cafile = "C:\php\extras\ssl\cacert.pem"
   ```
4. Restart your web server

### Option 2: Disable SSL Verification (DEVELOPMENT ONLY)
⚠️ **WARNING**: Never use this in production!

Edit the BkashPaymentTrait and EPSGatewayTrait files to add:
```php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
```

## Current Solution Implemented
The code has been updated to:
1. ✅ Save orders FIRST before payment processing
2. ✅ Handle payment gateway errors gracefully
3. ✅ Orders are saved even if payment gateway fails
4. ✅ COD orders no longer incorrectly call bKash API

This means:
- Your orders will save successfully regardless of SSL issues
- Payment gateway errors are logged but don't break the checkout
- You can test locally without SSL certificate configuration

## Testing Payment Gateways
When you're ready to properly test bKash/EPS:
1. Use Option 1 above to configure SSL certificates
2. Or deploy to a production server with proper SSL
3. The payment gateway integration will work correctly

## Files Modified
- `CustomerCheckoutController.php` - Reordered payment flow
