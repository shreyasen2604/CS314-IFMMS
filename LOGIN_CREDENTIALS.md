# IFMMS-ZAR Login Credentials & Troubleshooting

## Default Login Credentials

### Admin Account
- **Email:** admin@zar.com
- **Password:** Admin@12345

### Driver Account
- **Email:** driver1@zar.com
- **Password:** Driver@12345

### Technician Account
- **Email:** tech1@zar.com
- **Password:** Tech@12345

---

## If You Cannot Login

### Option 1: Re-seed the Database (Recommended)

Run these commands in your terminal:

```bash
# This will recreate the default users
php artisan db:seed --class=UserSeeder

# Clear cache
php artisan cache:clear
```

### Option 2: Create a New Admin User via Tinker

```bash
php artisan tinker
```

Then paste this code:

```php
use App\Models\User;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@zar.com',
    'password' => bcrypt('Admin@12345'),
    'role' => 'Admin'
]);

exit
```

### Option 3: Reset Everything (Fresh Install)

⚠️ **WARNING: This will delete all data!**

```bash
# Reset database and seed with default data
php artisan migrate:fresh --seed

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## Common Login Issues & Solutions

### Issue 1: "These credentials do not match our records"

**Possible Causes:**
1. Users table is empty
2. Password was changed
3. Email doesn't exist in database

**Solution:**
```bash
# Check if users exist
php artisan tinker
>>> App\Models\User::all();
>>> exit

# If empty, seed the database
php artisan db:seed --class=UserSeeder
```

### Issue 2: Database Connection Error

**Solution:**
1. Check your `.env` file has correct database credentials
2. Ensure MySQL is running
3. Verify database name exists

### Issue 3: Password Not Working After Migration

**Solution:**
The password might not be hashed properly. Reset it:

```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@zar.com')->first();
>>> $user->password = bcrypt('Admin@12345');
>>> $user->save();
>>> exit
```

---

## Quick Fix Script

Create a file called `fix-login.php` in your project root:

```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Create or update admin user
$admin = User::updateOrCreate(
    ['email' => 'admin@zar.com'],
    [
        'name' => 'System Admin',
        'password' => bcrypt('Admin@12345'),
        'role' => 'Admin'
    ]
);

echo "Admin user created/updated successfully!\n";
echo "Email: admin@zar.com\n";
echo "Password: Admin@12345\n";
```

Then run:
```bash
php fix-login.php
```

---

## Verify Users in Database

To check existing users:

```bash
php artisan tinker
```

```php
// List all users
App\Models\User::select('id', 'name', 'email', 'role')->get();

// Check specific user
App\Models\User::where('email', 'admin@zar.com')->first();

exit
```

---

## Create Custom User

If you want to create a user with custom credentials:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Your Name',
    'email' => 'your.email@company.com',
    'password' => bcrypt('YourPassword123'),
    'role' => 'Admin' // or 'Driver' or 'Technician'
]);

exit
```

---

## Emergency Access

If all else fails, you can temporarily disable authentication (for development only):

1. Edit `app/Http/Controllers/AuthController.php`
2. In the `login` method, add this at the beginning:

```php
// TEMPORARY - REMOVE IN PRODUCTION
$user = \App\Models\User::first();
if ($user) {
    auth()->login($user);
    return redirect()->route(strtolower($user->role).'.dashboard');
}
```

⚠️ **Remember to remove this after fixing the issue!**

---

## Contact Support

If none of these solutions work:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection: `php artisan db:show`
3. Test database access: `php artisan migrate:status`

---

**Last Updated:** January 2025
**System:** IFMMS-ZAR v1.0