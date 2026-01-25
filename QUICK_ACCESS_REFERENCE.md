# Quick Panel Access Reference

## 🚀 Quick Access URLs

### Production URLs (usupershop.com)

| Panel | Login URL | Dashboard URL | Registration |
|-------|-----------|---------------|--------------|
| **Admin** | https://usupershop.com/login | https://usupershop.com/home | Manual creation only |
| **Seller/Vendor** | https://usupershop.com/login | https://usupershop.com/seller-dashboard | https://usupershop.com/seller/signup |
| **Dropshipper** | https://usupershop.com/login | https://usupershop.com/dropshipper-dashboard | https://usupershop.com/seller/signup |
| **Customer** | https://usupershop.com/customer-login | https://usupershop.com/customer/dashboard | https://usupershop.com/customer-signup |

---

## 🔑 Login Credentials Format

All panels use the same login format:
- **Username Field:** Email OR Mobile Number
- **Password Field:** User password

---

## 🎯 Auto-Redirect After Login

The system automatically redirects based on user type:

```
Admin      → /home
Seller     → /seller-dashboard
Vendor     → /seller-dashboard
Dropshipper → /dropshipper-dashboard
Customer   → /customer/dashboard
```

---

## 📝 Registration URLs

| User Type | Registration URL | Notes |
|-----------|------------------|-------|
| Customer | `/customer-signup` | Direct registration |
| Seller | `/seller/signup` | Requires admin approval |
| Vendor | `/seller/signup` | Same as seller |
| Dropshipper | `/seller/signup` | Admin assigns role |
| Admin | N/A | Created manually in database |

---

## 🔐 Password Reset

**URL:** `/forget/email`

**Process:**
1. Enter email or mobile
2. Receive OTP
3. Verify OTP
4. Set new password

---

## 📱 Social Login (Customers Only)

- **Google:** `/login/google`
- **Facebook:** `/login/facebook`

---

## 🚪 Logout

- **Standard:** `/logout` (POST with CSRF)
- **Alternative:** `/logout2` (GET)

---

## ⚙️ Account Requirements

### Admin
- ✅ Status = 1 (Active)
- ✅ Usertype = 'admin'

### Seller/Vendor
- ✅ Status = 1 (Active)
- ✅ Payment Status = 2 (Paid)
- ✅ Email verified (code = NULL)
- ✅ Admin approved

### Dropshipper
- ✅ Status = 1 (Active)
- ✅ Payment Status = 2 (Paid)
- ✅ Email verified (code = NULL)
- ✅ Admin approved
- ✅ Referral code assigned

### Customer
- ✅ Status = 1 (Active)
- ✅ Email/OTP verified (code = NULL)

---

## 🔍 Database User Types

```sql
-- Check user type
SELECT id, name, email, mobile, usertype, status, payment_status 
FROM users 
WHERE email = 'your@email.com';

-- User types:
-- 'admin'       - System administrator
-- 'seller'      - Seller/Shop owner
-- 'vendor'      - Vendor (same as seller)
-- 'dropshipper' - Dropshipper/Reseller
-- 'customer'    - End customer
```

---

## ⚠️ Common Issues

### Cannot Login?
1. Check email/mobile is correct
2. Verify password
3. Check `status` = 1 in database
4. For sellers: Check `payment_status` = 2
5. Ensure `code` field is NULL (verified)

### Wrong Dashboard?
1. Check `usertype` in database
2. Clear browser cache
3. Logout and login again

### OTP Not Received?
1. Check email settings in `.env`
2. Check SMS gateway configuration
3. Verify email/mobile format

---

## 📊 Status Codes

### User Status
- `0` - Pending/Not verified
- `1` - Active
- `2` - Suspended

### Payment Status (Sellers/Dropshippers)
- `0` - Not paid
- `1` - Pending
- `2` - Paid/Approved

---

## 🛠️ For Developers

### Create Test Users

```php
// Admin
User::create([
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'mobile' => '01700000001',
    'password' => Hash::make('password'),
    'usertype' => 'admin',
    'status' => 1
]);

// Seller
User::create([
    'name' => 'Seller User',
    'email' => 'seller@test.com',
    'mobile' => '01700000002',
    'password' => Hash::make('password'),
    'usertype' => 'seller',
    'status' => 1,
    'payment_status' => 2
]);

// Dropshipper
User::create([
    'name' => 'Dropshipper User',
    'email' => 'dropshipper@test.com',
    'mobile' => '01700000003',
    'password' => Hash::make('password'),
    'usertype' => 'dropshipper',
    'status' => 1,
    'payment_status' => 2
]);

// Customer
User::create([
    'name' => 'Customer User',
    'email' => 'customer@test.com',
    'mobile' => '01700000004',
    'password' => Hash::make('password'),
    'usertype' => 'customer',
    'status' => 1
]);
```

---

## 📞 Need Help?

Check the full documentation: `PANEL_ACCESS_GUIDE.md`

---

**Quick Tip:** All users can login from the main `/login` page. The system automatically redirects to the correct dashboard based on user type!
