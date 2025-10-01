# Communication Module Setup Instructions

## Overview
The Communication & User Management module has been successfully integrated into the IFMMS system. To activate it, you need to run the database migrations to create the necessary tables.

## Setup Steps

### 1. Run Database Migrations

Open your terminal/command prompt in the project root directory and run:

```bash
# Run all pending migrations to create the communication tables
php artisan migrate

# If you encounter any issues, you can try:
php artisan migrate:fresh --seed
# WARNING: This will drop all tables and recreate them with seed data
```

### 2. Clear Application Caches

After running migrations, clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Create Storage Link (for file uploads)

```bash
php artisan storage:link
```

### 4. Optional: Create Test Data

If you want to create some test messages and announcements:

```bash
php artisan tinker
```

Then in the tinker console:

```php
// Create a test message
$message = \App\Models\Message::create([
    'sender_id' => 1,
    'receiver_id' => 2,
    'subject' => 'Welcome to Communication Module',
    'body' => 'This is a test message to verify the system is working.',
    'type' => 'direct',
    'priority' => 'normal'
]);

// Create a test announcement
$announcement = \App\Models\Announcement::create([
    'title' => 'System Update',
    'content' => 'The new communication module has been activated.',
    'created_by' => 1,
    'type' => 'info',
    'target_audience' => 'all',
    'is_active' => true
]);

exit
```

## Tables Created

The following tables will be created:

1. **messages** - Stores all messages between users
2. **announcements** - Stores system-wide announcements
3. **notifications** - Stores user notifications
4. **user_preferences** - Stores user notification preferences

## Features Available After Setup

### For All Users:
- 📨 Send and receive direct messages
- 📢 View announcements
- 🔔 Receive notifications
- ⚙️ Configure notification preferences
- 📊 Communication dashboard

### For Admins:
- 📣 Create announcements
- 🎯 Target specific user groups
- 📊 Monitor communication activity

## Troubleshooting

### Error: Table 'messages' doesn't exist
**Solution**: Run `php artisan migrate`

### Error: Class 'App\Models\Message' not found
**Solution**: Run `composer dump-autoload`

### Error: Storage link not working
**Solution**: Run `php artisan storage:link`

### Error: Notifications not working
**Solution**: Ensure queue worker is running if using queued notifications:
```bash
php artisan queue:work
```

## Accessing the Module

After setup, you can access the communication features:

1. **Communication Hub**: Click on "Communication Hub" in the sidebar
2. **Messages**: Access your inbox and send messages
3. **Announcements**: View company announcements
4. **Notification Settings**: Configure your preferences

## Database Schema

### Messages Table
- Direct messaging between users
- Message priorities and status tracking
- File attachments support
- Reply threading

### Announcements Table
- Company-wide announcements
- Role-based targeting
- Scheduled publishing
- View tracking

### User Preferences Table
- Email/SMS/Push notification settings
- Quiet hours configuration
- Language and timezone preferences

## Next Steps

1. Test the messaging system by sending a message to another user
2. Create your first announcement (Admin only)
3. Configure your notification preferences
4. Check that file uploads work correctly

## Support

If you encounter any issues:
1. Check the Laravel log file: `storage/logs/laravel.log`
2. Ensure all migrations ran successfully: `php artisan migrate:status`
3. Verify file permissions on storage directories
4. Check that the database connection is working

---

**Module Version**: 1.0.0
**Last Updated**: January 2025
**Compatible with**: IFMMS-ZAR v1.0