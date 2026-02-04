# Dropshipper Price Validation and Profit Distribution System

## Overview
This document describes the implementation of the dropshipper price validation and profit distribution system for usupershop_v2.

## Features Implemented

### 1. **Allowable Price Range Validation**

#### Product Details Page
- Dropshippers see the wholesale price (sale_price) instead of regular customer price
- Clear display of allowable price range (min_price to max_price)
- Visual warnings about price restrictions:
  - ✓ Green text showing allowed range
  - ⚠ Red warning about rejection if price is outside range
- Real-time profit calculation as dropshipper enters selling price

#### Add to Cart Validation
**File:** `app/Http/Controllers/AddToCartController.php`

```php
// Validates selling price is within allowable range
if ($sellingPrice < $minPrice) {
    // Error: Price too low
}
if ($sellingPrice > $maxPrice) {
    // Error: Price too high - order will be rejected
}
```

**Validation Rules:**
- Selling price MUST be >= product's `min_price`
- Selling price MUST be <= product's `max_price`
- If price is below minimum: Shows error "Selling price cannot be less than ৳{min_price}"
- If price is above maximum: Shows error "Selling price cannot exceed ৳{max_price}. Order will not be accepted."

### 2. **Checkout Price Validation**

#### Order Checkout Process
**File:** `app/Http/Controllers/CustomerCheckoutController.php`

During checkout, the system re-validates all cart items:

```php
// Double-check price validation during checkout
if ($sellingPrice < $minPrice || $sellingPrice > $maxPrice) {
    return response()->json([
        'status' => false,
        'type' => 'price_validation',
        'message' => "Product '{$product->name}' selling price must be between ৳{$minPrice} and ৳{$maxPrice}. Please update your cart."
    ]);
}
```

**Why Double Validation?**
- Prevents price manipulation between adding to cart and checkout
- Ensures data integrity
- Protects against concurrent price changes by admin

### 3. **Profit Calculation and Storage**

#### During Order Placement
When a dropshipper places an order:

1. **Calculate Profit Per Item:**
   ```php
   $cost_price = $variantPrice; // Wholesale price
   $selling_price = $cart->drop_selling_price; // Dropshipper's price
   $dropshipper_profit = ($selling_price - $cost_price) * $cart->qty;
   ```

2. **Store in Order:**
   - `orders.dropshipper_id` - Links order to dropshipper
   - `orders.dropshipper_profit` - Total profit for entire order
   - `order_details.dropshipper_sell_price` - Selling price per item
   - `order_details.dropshipper_profit` - Profit per item

### 4. **Commission Distribution (Only on Delivery)**

#### Admin Order Status Update
**File:** `app/Http/Controllers/Backend/OrderController.php`

**Critical Rule:** Profit is ONLY added to dropshipper's account when order status is changed to "delivered"

```php
public function statusUpdate(Request $request)
{
    foreach ($orders as $order) {
        $order->status = $delivery_status;
        $order->save();

        // ONLY distribute profit if status is 'delivered'
        if ($delivery_status === 'delivered') {
            $this->order_amount_distribution($order);
        }
    }
}
```

#### Profit Distribution Logic

```php
private function order_amount_distribution(Order $order)
{
    // 1. Check if order has dropshipper
    if (!$order->dropshipper_id) {
        return; // Skip if not a dropshipper order
    }

    // 2. Prevent duplicate payments
    $existingProfit = DropshipperProfit::where('order_id', $order->id)
        ->where('status', 'paid')
        ->first();
        
    if ($existingProfit) {
        return; // Already paid, skip
    }

    // 3. Get dropshipper
    $dropshipper = User::find($order->dropshipper_id);
    
    // 4. Get total profit
    $totalProfit = floatval($order->dropshipper_profit);
    
    if ($totalProfit <= 0) {
        return; // No profit to distribute
    }

    // 5. Add profit to dropshipper's balance
    $dropshipper->increment('balance', $totalProfit);

    // 6. Record the transaction
    DropshipperProfit::create([
        'dropshipper_id' => $order->dropshipper_id,
        'order_id' => $order->id,
        'total_profit' => $totalProfit,
        'status' => 'paid',
        'paid_at' => now(),
    ]);
}
```

### 5. **Status Change Behavior**

| Status Change | Profit Added? | Reason |
|--------------|---------------|---------|
| Pending → Confirmed | ❌ No | Order not yet delivered |
| Pending → Packaging | ❌ No | Order not yet delivered |
| Confirmed → Packaging | ❌ No | Order not yet delivered |
| Packaging → **Delivered** | ✅ **YES** | **Order successfully delivered** |
| Pending → Canceled | ❌ No | Order canceled |
| Confirmed → Canceled | ❌ No | Order canceled |
| Delivered → Return | ❌ No | Already paid (no reversal in this version) |
| Any → Return | ❌ No | Order returned |

**Important Notes:**
- Profit is ONLY added when status changes TO "delivered"
- Changing status to confirmed, packaging, canceled, or return does NOT add profit
- System prevents duplicate profit payments using `DropshipperProfit` table
- All transactions are logged for audit trail

## Database Schema

### Orders Table
```sql
dropshipper_id - Foreign key to users table
dropshipper_profit - Total profit for the order (DECIMAL)
```

### Order Details Table
```sql
dropshipper_sell_price - Price dropshipper sold at (DECIMAL)
dropshipper_profit - Profit for this line item (DECIMAL)
```

### Dropshipper Profits Table
```sql
id - Primary key
dropshipper_id - Foreign key to users
order_id - Foreign key to orders
total_profit - Amount paid (DECIMAL)
status - 'paid' or 'pending'
paid_at - Timestamp when paid
created_at - Record creation time
updated_at - Record update time
```

### Products Table
```sql
min_price - Minimum allowable selling price (DECIMAL)
max_price - Maximum allowable selling price (DECIMAL)
sale_price - Wholesale price for dropshippers (DECIMAL)
```

## User Experience Flow

### For Dropshippers:

1. **View Product:**
   - See wholesale price (sale_price)
   - See allowable price range clearly displayed
   - See warnings about price restrictions

2. **Enter Selling Price:**
   - Input their desired selling price
   - See real-time profit calculation
   - Get immediate feedback if price is invalid

3. **Add to Cart:**
   - System validates price is within range
   - Shows error if price is too low or too high
   - Cannot add to cart with invalid price

4. **Checkout:**
   - System re-validates all prices
   - Prevents checkout if any price is invalid
   - Order is created with profit calculated

5. **After Delivery:**
   - When admin marks order as "delivered"
   - Profit is automatically added to their balance
   - Can view profit in their account

### For Admin:

1. **Manage Orders:**
   - Can change order status as needed
   - System automatically handles profit distribution
   - Only "delivered" status triggers profit payment

2. **Prevent Errors:**
   - System prevents duplicate payments
   - All transactions are logged
   - Can track profit payments in `dropshipper_profits` table

## Security Features

1. **Price Validation:**
   - Validated on add to cart
   - Re-validated on checkout
   - Prevents price manipulation

2. **Duplicate Prevention:**
   - Checks for existing profit payment
   - Prevents double-crediting accounts

3. **Transaction Logging:**
   - All profit distributions logged
   - Includes timestamps and amounts
   - Audit trail for accounting

4. **Status-Based Control:**
   - Only "delivered" status triggers payment
   - Other status changes don't affect balance
   - Clear business logic

## Testing Checklist

### Price Validation Tests:
- [ ] Try adding product with price below min_price
- [ ] Try adding product with price above max_price
- [ ] Try adding product with price within range
- [ ] Verify error messages are clear
- [ ] Test checkout validation

### Profit Distribution Tests:
- [ ] Place order as dropshipper
- [ ] Change status to "confirmed" - verify no profit added
- [ ] Change status to "packaging" - verify no profit added
- [ ] Change status to "delivered" - verify profit IS added
- [ ] Try changing to "delivered" again - verify no duplicate payment
- [ ] Change status to "canceled" - verify no profit added
- [ ] Change status to "return" - verify no profit added

### Edge Cases:
- [ ] Order with multiple products
- [ ] Order with variants (color/size)
- [ ] Order with zero profit
- [ ] Order with negative profit (shouldn't happen with validation)
- [ ] Concurrent status changes

## Files Modified

1. **app/Http/Controllers/AddToCartController.php**
   - Added price range validation for dropshippers

2. **app/Http/Controllers/CustomerCheckoutController.php**
   - Added checkout price validation
   - Enhanced profit calculation

3. **app/Http/Controllers/Backend/OrderController.php**
   - Updated `order_amount_distribution()` method
   - Added proper profit distribution logic
   - Added duplicate payment prevention

4. **resources/views/frontend/single_page/product-details.blade.php**
   - Enhanced price range display
   - Added visual warnings
   - Improved user feedback

5. **app/Models/DropshipperProfit.php**
   - Already exists, used for tracking payments

## Configuration

No additional configuration required. The system uses existing database fields:
- `products.min_price`
- `products.max_price`
- `products.sale_price`

## Maintenance Notes

### To Adjust Price Ranges:
1. Update product's `min_price` and `max_price` in admin panel
2. Changes take effect immediately
3. Existing cart items will be re-validated on checkout

### To View Profit History:
```sql
SELECT * FROM dropshipper_profits 
WHERE dropshipper_id = ? 
ORDER BY paid_at DESC;
```

### To Check Unpaid Orders:
```sql
SELECT o.* FROM orders o
LEFT JOIN dropshipper_profits dp ON o.id = dp.order_id
WHERE o.dropshipper_id IS NOT NULL
AND o.status = 'delivered'
AND dp.id IS NULL;
```

## Support

For issues or questions:
1. Check logs in `storage/logs/laravel.log`
2. Review `DropshipperProfit` records
3. Verify product price ranges are set correctly
4. Ensure order status is being updated properly

## Version History

- **v1.0** (Current) - Initial implementation
  - Price validation on add to cart
  - Price validation on checkout
  - Profit distribution on delivery only
  - Duplicate payment prevention
  - Transaction logging
