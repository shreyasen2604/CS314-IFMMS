#!/bin/bash

echo "========================================"
echo "IFMMS-ZAR Database Setup"
echo "========================================"
echo ""
echo "Creating all database tables..."
echo ""

php artisan migrate

echo ""
echo "Seeding database with default data..."
echo ""

php artisan db:seed

echo ""
echo "Clearing caches..."
echo ""

php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo ""
echo "Creating storage link..."
echo ""

php artisan storage:link

echo ""
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "You can now login with:"
echo "  Admin: admin@zar.com / Admin@12345"
echo "  Driver: driver1@zar.com / Driver@12345"
echo "  Technician: tech1@zar.com / Tech@12345"
echo ""