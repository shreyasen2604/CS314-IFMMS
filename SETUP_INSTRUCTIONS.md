# IFMMS-ZAR Setup Instructions

## Quick Setup (No Build Required)

The application is now configured to work immediately without building assets. All CSS and JavaScript dependencies are loaded via CDN.

### 1. Database Setup

Run these commands in your terminal:

```bash
# Navigate to project directory
cd c:\Users\salve\Downloads\IFMMS----Version1-main

# Run migrations to create tables
php artisan migrate

# Seed the database with sample data
php artisan db:seed --class=MaintenanceSeeder
```

### 2. Start the Application

```bash
# Start the Laravel development server
php artisan serve
```

The application will be available at: `http://localhost:8000`

### 3. Default Login Credentials

Based on the existing UserSeeder, you can login with:

**Admin:**
- Email: `admin@zar.com`
- Password: `Admin@12345`

**Driver:**
- Email: `driver1@zar.com`
- Password: `Driver@12345`

**Technician:**
- Email: `tech1@zar.com`
- Password: `Tech@12345`

## Optional: Build Assets (For Production)

If you want to build the assets locally instead of using CDN:

```bash
# Install Node.js dependencies
npm install

# Build assets for production
npm run build

# Or run in development mode with hot reload
npm run dev
```

Then uncomment the Vite directive in `resources/views/layouts/app.blade.php`:
```php
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Features Available

### All Users:
- ✅ Login/Logout functionality
- ✅ Role-based dashboard
- ✅ Maintenance dashboard (view-only for drivers)
- ✅ Analytics and reports
- ✅ Maintenance records

### Admin Only:
- ✅ User management
- ✅ Vehicle management
- ✅ Alert configuration
- ✅ System administration
- ✅ Export capabilities

### Technician Only:
- ✅ Work queue management
- ✅ Task assignment and completion
- ✅ Maintenance scheduling
- ✅ Alert management

### Driver Only:
- ✅ Personal vehicle status
- ✅ Maintenance request system
- ✅ Incident reporting
- ✅ Vehicle health monitoring

## Troubleshooting

### Common Issues:

1. **Database Connection Error:**
   - Check `.env` file for correct database credentials
   - Ensure database server is running

2. **Migration Errors:**
   - Run `php artisan migrate:fresh` to reset database
   - Check database permissions

3. **Missing Routes:**
   - Run `php artisan route:clear`
   - Run `php artisan config:clear`

4. **Permission Errors:**
   - Ensure storage and bootstrap/cache directories are writable
   - Run `php artisan storage:link`

### Development Commands:

```bash
# Clear all caches
php artisan optimize:clear

# Generate application key (if needed)
php artisan key:generate

# Create storage link
php artisan storage:link

# View all routes
php artisan route:list
```

## Next Steps

1. **Access the Application**: Navigate to `http://localhost:8000`
2. **Login**: Use the credentials above
3. **Explore Features**: Each role has different capabilities
4. **Add Data**: Use the seeders or manually add vehicles/users
5. **Customize**: Modify views, add charts, or extend functionality

The maintenance module is fully functional and ready to use!






