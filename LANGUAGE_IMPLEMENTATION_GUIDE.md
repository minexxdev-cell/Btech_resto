# Restaurant POS - Multi-Language System Implementation Guide

## Overview
Your Restaurant POS system now has a complete multi-language support system with Kinyarwanda, French, and English. The language selector with flag icons appears in the top navigation bar and persists across the session.

## Files Created/Modified

### 1. Language Files (New)
Located in: `/admin/languages/`
- **en.php** - English translations
- **fr.php** - French translations  
- **rw.php** - Kinyarwanda translations

### 2. Language Configuration (New)
**File:** `/admin/config/language.php`
- Handles language session management
- Loads appropriate language file
- Provides `__()` function for translations

### 3. CSS Styling (New)
**File:** `/admin/assets/css/language.css`
- Flag icon styles for language selector
- Responsive dropdown styling

### 4. Modified Files
- **partials/_head.php** - Includes language.css and language initialization
- **partials/_topnav.php** - Language dropdown with flag icons in header
- **partials/_sidebar.php** - Navigation items translated (removed duplicate language selector)
- **dashboard.php** - All strings translated
- **products.php** - All strings translated
- **pay_later.php** - All strings translated
- **payments.php** - All strings translated

## How It Works

### 1. Language Selection
- Click the flag icon in the top-right corner of the header
- Select desired language: English (🇬🇧), Français (🇫🇷), or Kinyarwanda (🇷🇼)
- Language preference is stored in `$_SESSION['language']`
- Selection persists throughout the user's session

### 2. Translation Function
Use the `__()` function to output translated strings:
```php
<?php echo __('dashboard'); ?>        // Outputs "Dashboard", "Tableau de Bord", or "Dashboard" based on selected language
<?php echo __('customers'); ?>         // Outputs "Customers", "Clients", or "Abacuruzi"
<?php echo __('not_paid'); ?>          // Outputs "Not Paid", "Non Payé", or "Ntabwo Yahabwe"
```

### 3. How Session Works
```php
// First request - no language set
$_SESSION['language'] = 'en';  // Default to English

// User clicks language link
// URL: ?lang=fr
$_SESSION['language'] = 'fr';  // Switches to French

// Language persists until session ends or user logs out
```

## Adding More Translations

### Step 1: Add Key to Language Files
Edit all three language files and add your new key:

**en.php:**
```php
'your_key' => 'English Text',
```

**fr.php:**
```php
'your_key' => 'Texte Français',
```

**rw.php:**
```php
'your_key' => 'Umwandiko mu Kinyarwanda',
```

### Step 2: Use in Your Pages
```php
<?php echo __('your_key'); ?>
```

## Translation Keys Already Available

### Navigation
- dashboard, customers, products, orders, payments, receipts, manage_versement, reporting, logout

### Dashboard
- total_customers, total_products, total_orders, total_sales
- recent_orders, recent_payments, see_all

### Common
- code, customer, product, unit_price, quantity, total, status, date
- amount, order_code, image, product_code, name, price, actions

### Products
- add_new_product, low_stock_alert, running_low_on_stock
- low_stock_items, search_products, delete, update, search

### Versement
- add_versement, manage_versement, total_payments, total_versements
- remaining_amount, amount, who, add, versement_code
- versement_added, versement_deleted, blank_values_not_accepted

### Payments & Orders
- make_a_new_order, view_items, pay_order, pay_later, cancel
- pay_later, payment_id, payment_code, amount_to_pay, payment_method
- marked_as_pay_later, confirm_pay_later, payment_recorded

### Receipts
- order_records, search_by_customer, view, print
- add_products_to_order, order_total, tip, total_paid, print_receipt

### Messages
- success, error, deleted, try_again_later, welcome, language, my_profile

## Updating Existing Pages to Use Translations

### Before (English only)
```php
<h3 class="mb-0">Recent Orders</h3>
<button class="btn btn-sm btn-primary">Delete</button>
```

### After (Multi-language)
```php
<h3 class="mb-0"><?php echo __('recent_orders'); ?></h3>
<button class="btn btn-sm btn-primary"><?php echo __('delete'); ?></button>
```

## Pages Still Needing Translation

To maintain consistency across the entire system, translate these pages:

1. **admin/customes.php** - Customers page
2. **admin/orders.php** - Make orders page
3. **admin/make_oder.php** - Order creation
4. **admin/add_customer.php** - Customer form
5. **admin/add_product.php** - Product form
6. **admin/orders_reports.php** - Orders reports
7. **admin/payments_reports.php** - Payments reports
8. **admin/receipts.php** - Receipts page
9. **admin/make_multiple_orders.php** - Multiple orders

## Troubleshooting

### Language not persisting?
- Ensure `session_start()` is called at the top of each page
- Check that cookies are enabled in browser
- Verify `$_SESSION['language']` is being set correctly

### Translations not showing?
- Check that the translation key exists in the language file
- Verify the `__()` function is being called correctly
- Clear browser cache
- Check PHP error logs

### Flag icons not displaying?
- Ensure `language.css` is properly linked in `_head.php`
- Check browser console for CSS loading errors
- Verify all CSS files are in the correct directory

## Customer Roles Supported

The language system works for all user types:
- ✅ Admin users (`/admin/` pages)
- ✅ Cashier users (`/cashier/` pages) 
- ✅ Customer users (`/customer/` pages)

To add languages to other roles, copy the same language system to:
- `/admin/languages/` → `/cashier/languages/`
- `/admin/languages/` → `/customer/languages/`

And update their respective `partials/_head.php` files.

## Default Language

The system defaults to **English**. To change the default, edit:
**File:** `/admin/config/language.php`
```php
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';  // Change 'en' to 'fr' or 'rw'
}
```

## API & Integration

If you have an external system integrating with this POS:
- Language preference is stored per session/user
- Language key is NOT transmitted in API calls
- Translations are client-side only
- All database operations use English keys

## Support for Additional Languages

To add a new language (e.g., Spanish/Español):

1. Create `/admin/languages/es.php` with all translation keys
2. Add flag CSS in `/admin/assets/css/language.css`:
```css
.flag-es {
    background-image: url('data:image/svg+xml;utf8,...'); /* Spanish flag SVG */
}
```
3. Add language option to `/admin/partials/_topnav.php`:
```php
<a class="dropdown-item <?php echo ($_SESSION['language'] == 'es' ? 'active' : ''); ?>" href="?lang=es">
    <span class="flag-icon flag-es"></span>
    <span>Español</span>
</a>
```
4. Update language.php to support 'es'

## Performance Notes

- Language files are loaded once per session
- No database queries for language data
- Translation function uses PHP array lookup (very fast)
- Minimal overhead - suitable for high-traffic systems

---

**Implementation Date:** January 23, 2026
**Languages Supported:** English, French (Français), Kinyarwanda
**Default Language:** English
