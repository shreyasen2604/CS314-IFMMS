# Predictive Maintenance & Scheduling Module

## Overview
This module provides comprehensive predictive maintenance and scheduling capabilities for the ZAR Logistics fleet management system. It includes vehicle health monitoring, maintenance scheduling, cost forecasting, and alert management.

## Features Implemented

### 1. Maintenance Dashboard (`/maintenance/dashboard`)
- Fleet overview with key metrics
- Vehicle health distribution
- Upcoming maintenance alerts
- Monthly cost tracking
- Recent alerts and scheduled maintenance

### 2. Vehicle Maintenance Profile (`/maintenance/vehicles/{id}/profile`)
- Individual vehicle maintenance history
- Current health status with visual indicators
- Active alerts and upcoming schedules
- Cost analysis and statistics
- Timeline view of maintenance activities

### 3. Maintenance Scheduler (`/maintenance/schedule`)
- Interactive calendar view with drag-and-drop functionality
- Create and manage maintenance appointments
- Bulk scheduling capabilities
- Recurring maintenance setup
- Technician assignment
- Priority-based scheduling

### 4. Predictive Analytics (`/maintenance/analytics`)
- Vehicle health scoring system
- Maintenance cost forecasting
- Performance trend analysis
- Breakdown risk assessment
- Maintenance type distribution charts

### 5. Maintenance Records (`/maintenance/records`)
- Complete maintenance history
- Advanced search and filtering
- Cost tracking and analysis
- Export capabilities (CSV/PDF)
- Detailed record views

### 6. Alert Management (`/maintenance/alerts`)
- Configurable alert thresholds
- Real-time notification system
- Bulk alert management
- Alert history tracking
- Emergency alert handling

## Database Schema

### Tables Created:
1. **vehicles** - Vehicle information and health scores
2. **maintenance_records** - Historical maintenance data
3. **maintenance_schedules** - Scheduled maintenance appointments
4. **maintenance_alerts** - System alerts and notifications

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=MaintenanceSeeder
```

### 3. Install Dependencies
The module uses the following frontend libraries:
- Bootstrap 5.3.0
- Chart.js
- FullCalendar
- DataTables
- Font Awesome

These are loaded via CDN in the layout file.

## File Structure

```
app/
├── Http/Controllers/
│   └── MaintenanceController.php
├���─ Models/
│   ├── Vehicle.php
│   ├── MaintenanceRecord.php
│   ├── MaintenanceSchedule.php
│   └── MaintenanceAlert.php
database/
├── migrations/
│   ├── 2024_01_01_000001_create_vehicles_table.php
│   ├── 2024_01_01_000002_create_maintenance_records_table.php
│   ├── 2024_01_01_000003_create_maintenance_schedules_table.php
│   └── 2024_01_01_000004_create_maintenance_alerts_table.php
└── seeders/
    └── MaintenanceSeeder.php
resources/views/
├── layouts/
│   └── app.blade.php
└── maintenance/
    ├── dashboard.blade.php
    ├── vehicle-profile.blade.php
    ├── scheduler.blade.php
    ├── analytics.blade.php
    ├── records.blade.php
    └── alerts.blade.php
```

## Routes

All maintenance routes are prefixed with `/maintenance` and require authentication:

- `GET /maintenance/dashboard` - Main dashboard
- `GET /maintenance/vehicles/{id}/profile` - Vehicle profile
- `GET /maintenance/schedule` - Scheduler interface
- `GET /maintenance/analytics` - Analytics dashboard
- `GET /maintenance/records` - Maintenance records
- `GET /maintenance/alerts` - Alert management

## Key Features

### Predictive Analytics
- Health scoring algorithm based on multiple factors
- Breakdown risk assessment
- Cost forecasting using historical data
- Performance trend analysis

### Smart Scheduling
- Drag-and-drop calendar interface
- Automatic conflict detection
- Recurring maintenance patterns
- Technician workload balancing

### Alert System
- Configurable thresholds for various metrics
- Real-time notifications
- Escalation procedures
- Bulk management capabilities

### Reporting & Export
- Comprehensive maintenance reports
- Cost analysis and budgeting
- Export to CSV and PDF formats
- Custom date range filtering

## Usage

1. **Access the Module**: Navigate to `/maintenance/dashboard` after logging in
2. **View Fleet Status**: The dashboard provides an overview of all vehicles and their maintenance status
3. **Schedule Maintenance**: Use the scheduler to create and manage maintenance appointments
4. **Monitor Alerts**: Check the alerts page for any urgent maintenance needs
5. **Analyze Performance**: Use the analytics page to identify trends and optimize maintenance schedules
6. **Track Records**: View complete maintenance history and generate reports

## Customization

The module is designed to be easily customizable:

- **Alert Thresholds**: Modify alert criteria in the configuration modal
- **Maintenance Types**: Add new maintenance categories in the database enums
- **Health Scoring**: Adjust the health score calculation algorithm
- **Reporting**: Customize report formats and data fields

## Integration

The module integrates seamlessly with the existing IFMMS system:
- Uses existing user authentication and roles
- Shares the same database connection
- Follows Laravel best practices and conventions
- Responsive design compatible with existing UI

## Future Enhancements

Potential improvements for future versions:
- Machine learning-based predictive algorithms
- IoT sensor integration for real-time vehicle monitoring
- Mobile app for technicians
- Advanced reporting with business intelligence
- Integration with parts inventory management
- Automated maintenance scheduling based on usage patterns

## Support

For technical support or questions about the maintenance module, please refer to the main IFMMS documentation or contact the development team.