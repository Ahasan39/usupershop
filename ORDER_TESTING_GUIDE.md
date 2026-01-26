# Order Placement Testing Guide

## Steps to Test Order Placement

### 1. Clear Cache First
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. Test Order Flow

#### Step 1: Add Product to Cart
1. Browse products
2. Add a product to cart
3. Go to cart page

#### Step 2: Checkout
1. Click "Proceed to Checkout"
2. Fill in shipping details:
   - Full Name
   - Email (optional)
   - Phone Number
   - Address
3. Click "Submit"
4. Should redirect to payment page

#### Step 3: Payment
1. Select payment method (Cash on Delivery recommended for testing)
2. Click "Confirm Order"
3. Should redirect to success page

### 3. Check if Order Was Created

#### Check Database:
```sql
-- Check latest order
SELECT * FROM orders ORDER BY id DESC LIMIT 1;

-- Check order details
SELECT * FROM order_details ORDER BY id DESC LIMIT 5;

-- Check shipping
SELECT * FROM shippings ORDER BY id DESC LIMIT 1;

-- Check payment
SELECT * FROM payments ORDER BY id DESC LIMIT 1;
```

#### Check Logs:
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50

# Look for these log entries:
# - "Checkout Store Request"
# - "Shipping created"
# - "Payment Store Request"
# - "Payment created"
# - "Order created"
# - "Order detail created"
# - "COD order completed"
```

### 4. What to Look For

#### Success Indicators:
✅ Redirected to success page  
✅ Order record in `orders` table  
✅ Order details in `order_details` table  
✅ Shipping record in `shippings` table  
✅ Payment record in `payments` table  
✅ Product stock decreased  
✅ Cart cleared  

#### Failure Indicators:
❌ Error message displayed  
❌ No order in database  
❌ Error in logs  
❌ Cart not cleared  

### 5. Common Issues & Solutions

#### Issue: "Please add any product for payment!"
**Solution:** Cart is empty. Add products to cart first.

#### Issue: "Your cart is empty!"
**Solution:** Session expired. Add products again.

#### Issue: "Product out of stock"
**Solution:** Check product quantity in database. Increase stock if needed.

#### Issue: No shipping_id in session
**Solution:** Complete checkout step first before going to payment.

#### Issue: Order shows success but not in database
**Solution:** Check logs for errors. Look for database or transaction errors.

### 6. Debug Commands

```bash
# Check if cart has items (in tinker)
php artisan tinker
>>> Cart::content()
>>> Session::get('shipping_id')
>>> Session::get('delivery_charge')
>>> exit

# Check latest logs
Get-Content storage/logs/laravel.log -Tail 100 | Select-String "Order"

# Check database
php artisan tinker
>>> DB::table('orders')->latest()->first()
>>> DB::table('order_details')->latest()->first()
>>> exit
```

### 7. Test Scenarios

#### Scenario 1: COD Order (Recommended)
1. Add product to cart
2. Checkout with shipping details
3. Select "Cash on Delivery"
4. Confirm order
5. **Expected:** Success page with order ID

#### Scenario 2: Bkash Payment
1. Add product to cart
2. Checkout with shipping details
3. Select "Bkash"
4. Confirm order
5. **Expected:** Redirect to Bkash payment gateway

#### Scenario 3: Multiple Products
1. Add multiple products to cart
2. Checkout with shipping details
3. Select payment method
4. Confirm order
5. **Expected:** All products in order_details table

### 8. Verification Checklist

After placing an order, verify:

- [ ] Order ID displayed on success page
- [ ] Order exists in `orders` table
- [ ] Order details exist in `order_details` table
- [ ] Shipping info saved in `shippings` table
- [ ] Payment method saved in `payments` table
- [ ] Product stock decreased
- [ ] Cart is empty
- [ ] Session data cleared
- [ ] Logs show successful order creation

### 9. If Still Not Working

1. **Check logs immediately after order attempt:**
   ```bash
   Get-Content storage/logs/laravel.log -Tail 50
   ```

2. **Check for JavaScript errors:**
   - Open browser console (F12)
   - Look for any errors

3. **Check session:**
   ```php
   php artisan tinker
   >>> Session::all()
   ```

4. **Check if user is authenticated:**
   ```php
   php artisan tinker
   >>> Auth::check()
   >>> Auth::user()
   ```

5. **Enable debug mode:**
   - Set `APP_DEBUG=true` in `.env`
   - Try placing order again
   - Check error messages

### 10. Contact Support

If issue persists, provide:
1. Screenshot of error message
2. Last 100 lines of laravel.log
3. Database query results for orders table
4. Browser console errors (if any)

---

**Note:** Default delivery charge is set to 60 BDT if not specified in checkout form.
