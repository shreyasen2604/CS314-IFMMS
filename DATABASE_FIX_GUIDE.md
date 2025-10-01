# Database Fix Guide for IFMMS-ZAR

## 🚨 Current Issue
**Error:** Table 'ifmms_zar.incidents' doesn't exist

This error means the database tables haven't been created yet. Follow the steps below to fix this.

---

## 🔧 Quick Fix (Recommended)

Run this single command to fix everything:

```bash
php fix-database.php
```

This script will:
1. ✅ Run all migrations to create tables
2. ✅ Clear all caches
3. ✅ Seed default data
4. ✅ Create storage links
5. ✅ Verify all tables exist
6. ✅ Display login credentials

---

## 📋 Manual Fix (Step by Step)

If the quick fix doesn't work, follow these manual steps:

### Step 1: Check Database Connection

First, verify your database connection in `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ifmms_zar
DB_USERNAME=root
DB_PASSWORD=
```

### Step 2: Create Database (if not exists)

```sql
CREATE DATABASE IF NOT EXISTS ifmms_zar;
```

Or via command line:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ifmms_zar;"
```

### Step 3: Run Migrations

```bash
# Run all pending migrations
php artisan migrate

# If you get errors, try fresh migration (WARNING: Deletes all data)
php artisan migrate:fresh
```

### Step 4: Seed Default Data

```bash
# Seed all default data
php artisan db:seed

# Or seed specific classes
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=VehicleSeeder
php artisan db:seed --class=IncidentSeeder
php artisan db:seed --class=MaintenanceSeeder
```

### Step 5: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 6: Create Storage Link

```bash
php artisan storage:link
```

---

## 🔍 Verify Tables Exist

Check if all tables were created:

```bash
php artisan tinker
```

Then run:
```php
// Check if specific tables exist
Schema::hasTable('incidents');  // Should return true
Schema::hasTable('users');      // Should return true
Schema::hasTable('vehicles');   // Should return true
Schema::hasTable('messages');   // Should return true

// List all tables
DB::select('SHOW TABLES');

exit
```

---

## 📊 Required Tables

The system needs these tables to function:

| Table | Purpose | Migration File |
|-------|---------|---------------|
| users | User authentication | 0001_01_01_000000_create_users_table.php |
| vehicles | Fleet management | 2024_01_01_000001_create_vehicles_table.php |
| incidents | Incident reporting | 2025_08_16_083422_create_incidents_table.php |
| incident_updates | Incident comments | 2025_08_16_100024_create_incident_updates_table.php |
| maintenance_records | Maintenance history | 2024_01_01_000002_create_maintenance_records_table.php |
| maintenance_schedules | Scheduled maintenance | 2024_01_01_000003_create_maintenance_schedules_table.php |
| maintenance_alerts | System alerts | 2024_01_01_000004_create_maintenance_alerts_table.php |
| maintenance_types | Maintenance categories | 2024_01_15_000001_create_maintenance_types_table.php |
| vehicle_health_metrics | Vehicle health data | 2024_01_15_000002_create_vehicle_health_metrics_table.php |
| messages | User messaging | 2025_01_01_000001_create_messages_table.php |
| announcements | System announcements | 2025_01_01_000002_create_announcements_table.php |
| notifications | User notifications | 2025_01_01_000003_create_notifications_table.php |
| user_preferences | Notification settings | 2025_01_01_000004_create_user_preferences_table.php |

---

## 🆘 Troubleshooting

### Error: "Nothing to migrate"
**Solution:** Tables already exist. Check with `SHOW TABLES` in MySQL.

### Error: "Access denied for user"
**Solution:** Check database credentials in `.env` file.

### Error: "Unknown database 'ifmms_zar'"
**Solution:** Create the database first:
```bash
mysql -u root -p -e "CREATE DATABASE ifmms_zar;"
```

### Error: "Table already exists"
**Solution:** Use fresh migration (WARNING: Deletes data):
```bash
php artisan migrate:fresh --seed
```

### Error: "Class not found"
**Solution:** Regenerate autoload files:
```bash
composer dump-autoload
```

---

## 🔄 Complete Reset (Nuclear Option)

If nothing else works, completely reset everything:

```bash
# Drop and recreate database
mysql -u root -p -e "DROP DATABASE IF EXISTS ifmms_zar; CREATE DATABASE ifmms_zar;"

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Create storage link
php artisan storage:link

# Create default users
php fix-login.php
```

---

## ✅ After Successful Setup

You should be able to:
1. Login with default credentials
2. Access all dashboards without errors
3. Create incidents
4. Send messages
5. View maintenance schedules

### Default Credentials:
- **Admin:** admin@zar.com / Admin@12345
- **Driver:** driver1@zar.com / Driver@12345
- **Technician:** tech1@zar.com / Tech@12345

---

## 📝 Check Migration Status

To see which migrations have run:

```bash
php artisan migrate:status
```

Output should show:
```
+------+------------------------------------------------+-------+
| Ran? | Migration                                      | Batch |
+------+------------------------------------------------+-------+
| Yes  | 0001_01_01_000000_create_users_table         | 1     |
| Yes  | 2025_08_16_083422_create_incidents_table     | 1     |
| Yes  | 2025_01_01_000001_create_messages_table      | 1     |
... (all migrations should show "Yes")
```

---

## 🚀 Start the Application

After fixing the database:

```bash
# Start the development server
php artisan serve

# Access the application
http://localhost:8000
```

---

**Last Updated:** January 2025
**Version:** IFMMS-ZAR v1.0