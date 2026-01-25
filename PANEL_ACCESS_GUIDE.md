# U Super Shop v2 - Panel Access Guide

## 🔐 How to Access Different User Panels

This guide explains how to access and login to different user panels in the U Super Shop v2 system.

---

## 📍 Base URL
```
https://usupershop.com
or
http://localhost (for local development)
```

---

## 1️⃣ ADMIN PANEL ACCESS

### Login URL:
```
https://usupershop.com/login
```

### Login Credentials Format:
- **Email or Mobile:** admin@usupershop.com OR admin mobile number
- **Password:** Your admin password

### Login Process:
1. Navigate to `/login`
2. Enter admin email or mobile number in the "Email/Phone" field
3. Enter admin password
4. Click "Login" button
5. System will redirect to: `/home` (Admin Dashboard)

### Admin Dashboard Route:
```
Route: /home
Named Route: home
Controller: HomeController@index
Middleware: auth, admin
```

### Key Admin Features:
- Full system management
- User management (customers, sellers, vendors, dropshippers)
- Product approval and management
- Order management
- Commission settings
- Payment gateway configuration
- Reports and analytics
- Courier management

---

## 2️⃣ SELLER/VENDOR PANEL ACCESS

### Registration URL:
```
https://usupershop.com/seller/signup
Route Name: seller.signup
```

### Login URL:
```
https://usupershop.com/login
```

### Login Credentials Format:
- **Email or Mobile:** seller@example.com OR seller mobile number
- **Password:** Your seller password

### Login Process:
1. Navigate to `/login`
2. Enter seller email or mobile number
3. Enter seller password
4. Click "Login" button
5. System checks usertype and redirects to: `/seller-dashboard`

### Seller Dashboard Route:
```
Route: /seller-dashboard
Named Route: seller.dashboard
Controller: SellerDashboardController@sellerDashboard
Middleware: auth, seller
```

### Registration Process:
1. Go to `/seller/signup`
2. Fill in registration form:
   - Name
   - Email
   - Mobile Number
   - Password
   - Shop Name
   - Address
3. Submit form
4. Verify email/OTP
5. Pay subscription fee (if required)
6. Wait for admin approval
7. Login after approval

### Key Seller Features:
- Seller dashboard with sales analytics
- Add products from admin catalog to your shop
- Create vendor products (own products)
- Order management
- Commission tracking
- Wallet management
- Payment settings
- Transaction history
- Reports

---

## 3️⃣ DROPSHIPPER PANEL ACCESS

### Registration URL:
```
https://usupershop.com/seller/signup
(Same as seller, but select dropshipper type)
```

### Login URL:
```
https://usupershop.com/login
```

### Login Credentials Format:
- **Email or Mobile:** dropshipper@example.com OR dropshipper mobile number
- **Password:** Your dropshipper password

### Login Process:
1. Navigate to `/login`
2. Enter dropshipper email or mobile number
3. Enter dropshipper password
4. Click "Login" button
5. System checks usertype and redirects to: `/dropshipper-dashboard`

### Dropshipper Dashboard Route:
```
Route: /dropshipper-dashboard
Named Route: dropshipper.dashboard
Controller: DropshipperDashboardController@dropshipperDashboard
Middleware: auth, dropshipper
```

### Registration Process:
1. Register as seller/vendor
2. Admin assigns dropshipper role
3. Get referral code
4. Pay subscription fee (if required)
5. Login after approval

### Key Dropshipper Features:
- Dropshipper dashboard
- Browse and add products to personal shop
- Set custom product prices
- Referral code system
- Order management
- Commission tracking (reseller commission)
- Wallet management
- Transaction history
- Profit reports
- Vendor product management

---

## 4️⃣ CUSTOMER PANEL ACCESS

### Registration URL:
```
https://usupershop.com/customer-signup
Route Name: customer.signup
```

### Login URL:
```
https://usupershop.com/customer-login
Route Name: customer.login
```

### Alternative Login (Main Login):
```
https://usupershop.com/login
```

### Login Credentials Format:
- **Email or Mobile:** customer@example.com OR customer mobile number
- **Password:** Your customer password

### Login Process:
1. Navigate to `/customer-login` or `/login`
2. Enter customer email or mobile number
3. Enter customer password
4. Click "Login" button
5. System redirects to: `/customer/dashboard`

### Customer Dashboard Route:
```
Route: /customer/dashboard
Named Route: customer.dashboard
Controller: DashboardController@dashboard
Middleware: auth, customer
```

### Registration Process:
1. Go to `/customer-signup`
2. Fill in registration form:
   - Name
   - Email
   - Mobile Number
   - Password
3. Verify OTP (sent to email/mobile)
4. Login after verification

### Key Customer Features:
- Customer dashboard
- Profile management
- Order history
- Order tracking
- Wishlist management
- Password change
- Payment information

---

## 🔑 UNIFIED LOGIN SYSTEM

The system uses a **unified login** at `/login` that automatically redirects users based on their `usertype`:

### Login Logic Flow:
```php
if (usertype === 'customer') {
    redirect to: /customer/dashboard
}
elseif (usertype === 'vendor' OR usertype === 'seller') {
    redirect to: /seller-dashboard
}
elseif (usertype === 'dropshipper') {
    redirect to: /dropshipper-dashboard
}
elseif (usertype === 'admin') {
    redirect to: /home (admin dashboard)
}
```

---

## 📋 USER TYPES IN DATABASE

The `users` table has a `usertype` column with these values:

1. **admin** - System administrator
2. **seller** - Seller/Shop owner
3. **vendor** - Vendor (same as seller)
4. **dropshipper** - Dropshipper/Reseller
5. **customer** - End customer

---

## 🚪 LOGOUT

### Logout URL:
```
https://usupershop.com/logout
Route: /logout
Method: POST (requires CSRF token)
```

### Alternative Logout (for customers):
```
https://usupershop.com/logout2
Route Name: logout2
Method: GET
```

---

## 🔐 PASSWORD RESET

### Forgot Password URL:
```
https://usupershop.com/forget/email
Route Name: forget.email
```

### Password Reset Process:
1. Go to `/forget/email`
2. Enter email or mobile number
3. Receive OTP via email/SMS
4. Verify OTP at `/forget/verify/otp`
5. Set new password at `/password_changes/with/email/`
6. Login with new password

---

## 📱 SOCIAL LOGIN

### Google Login:
```
URL: /login/google
Callback: /login/google/callback
```

### Facebook Login:
```
URL: /login/facebook
Callback: /login/facebook/callback
```

**Note:** Social login is only available for customers and creates customer accounts automatically.

---

## ⚙️ MIDDLEWARE PROTECTION

Each panel is protected by specific middleware:

| Panel | Middleware | Route Prefix |
|-------|-----------|--------------|
| Admin | `auth, admin` | `/` (admin routes) |
| Seller | `auth, seller` | `/sellers/*` |
| Dropshipper | `auth, dropshipper` | `/dropshippers/*` |
| Customer | `auth, customer` | `/customer/*` |

---

## 🎯 QUICK ACCESS SUMMARY

| User Type | Login URL | Dashboard URL | Registration URL |
|-----------|-----------|---------------|------------------|
| **Admin** | `/login` | `/home` | N/A (created manually) |
| **Seller/Vendor** | `/login` | `/seller-dashboard` | `/seller/signup` |
| **Dropshipper** | `/login` | `/dropshipper-dashboard` | `/seller/signup` |
| **Customer** | `/customer-login` or `/login` | `/customer/dashboard` | `/customer-signup` |

---

## 🔍 TESTING ACCESS

### For Development/Testing:

1. **Create Admin User** (via database seeder or manual):
```sql
INSERT INTO users (name, email, mobile, password, usertype, status) 
VALUES ('Admin', 'admin@usupershop.com', '01700000000', '$2y$10$hashed_password', 'admin', 1);
```

2. **Create Seller User**:
   - Register via `/seller/signup`
   - Or insert via database with `usertype = 'seller'`

3. **Create Dropshipper User**:
   - Register as seller
   - Admin changes `usertype` to 'dropshipper'

4. **Create Customer User**:
   - Register via `/customer-signup`
   - Or insert via database with `usertype = 'customer'`

---

## ⚠️ IMPORTANT NOTES

1. **Email/OTP Verification**: Some user types require email or OTP verification before full access
2. **Subscription Fees**: Sellers and dropshippers may need to pay subscription fees
3. **Admin Approval**: Sellers and vendors require admin approval (`status = 1`)
4. **Payment Status**: Sellers need `payment_status = 2` for full access
5. **Account Status**: 
   - `status = 0` - Pending/Not verified
   - `status = 1` - Active
   - `status = 2` - Suspended

---

## 🛠️ TROUBLESHOOTING

### Cannot Login?
- Check if email/mobile is correct
- Verify password
- Check account status in database
- Ensure email is verified (check `code` field is NULL)
- For sellers: Check `payment_status` and admin approval

### Redirected to Wrong Dashboard?
- Check `usertype` in database
- Clear browser cache and cookies
- Check middleware configuration

### OTP Not Received?
- Check email configuration in `.env`
- Check SMS gateway configuration
- Verify email/mobile number is correct

---

## 📞 SUPPORT

For access issues, contact system administrator or check:
- Database user records
- Email configuration
- SMS gateway settings
- Middleware configuration

---

**Last Updated:** 2025
**Version:** 2.0
**Framework:** Laravel 8.x
