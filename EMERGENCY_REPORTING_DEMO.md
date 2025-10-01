# Emergency Vehicle Reporting System

## Overview
This system provides comprehensive emergency and incident reporting capabilities for drivers with three main components:

### 1. Quick Emergency Report
- **Purpose**: Fast checkbox-based reporting for immediate emergencies
- **Time**: ~30 seconds to complete
- **Features**:
  - Checkboxes for common emergencies: Fuel Low, Engine Trouble, Electrical Problem, Oil Low, Tire Issues, Overheating, Other
  - Vehicle ID input
  - Location with GPS button
  - Immediate assistance checkbox
  - Auto-severity assignment based on emergency type

### 2. Detailed Incident Report
- **Purpose**: Comprehensive reporting with photos and location data
- **Time**: ~3-5 minutes to complete
- **Features**:
  - Vehicle field selection
  - Incident type dropdown (Engine, Transmission, Brakes, Electrical, etc.)
  - Date/Time picker
  - Location with GPS button
  - Description textarea
  - Severity radio buttons (P1-Critical, P2-High, P3-Medium, P4-Low)
  - Assistance checkbox
  - File upload for photos (multiple files, up to 5MB each)

### 3. Success Confirmation
- **Features**:
  - Report ID generation (format: ER-YYYY-XXX)
  - Estimated response time based on severity
  - Navigation buttons to view all reports or create new report

## Technical Implementation

### Routes Added
```php
Route::get('/incidents/emergency', [DriverIncidentController::class, 'emergency'])->name('incidents.emergency');
Route::post('/incidents/emergency', [DriverIncidentController::class, 'emergencyStore'])->name('incidents.emergency.store');
```

### Controller Methods
- `emergency()` - Display the emergency reporting form
- `emergencyStore()` - Handle both quick and detailed emergency reports
- `storeQuickEmergencyReport()` - Process quick emergency reports
- `storeDetailedEmergencyReport()` - Process detailed incident reports

### Key Features
1. **Smart Severity Assignment**: Quick reports automatically assign severity based on emergency type
2. **GPS Integration**: Both forms include GPS location capture
3. **Photo Upload**: Detailed reports support multiple photo uploads
4. **Response Time Estimation**: Based on severity level (P1: 5-15 min, P2: 15-30 min, P3: 30-60 min)
5. **Report ID Generation**: Automatic generation of unique emergency report IDs

## Usage Instructions

### For Quick Emergency Reports:
1. Navigate to `/driver/incidents/emergency`
2. Select "Quick Emergency Report"
3. Fill in Vehicle ID
4. Add location (use GPS button for automatic coordinates)
5. Check applicable emergency types
6. Submit for immediate processing

### For Detailed Reports:
1. Navigate to `/driver/incidents/emergency`
2. Select "Detailed Incident Report"
3. Fill in all required fields
4. Set severity level using radio buttons
5. Upload photos if available
6. Submit for comprehensive processing

## Access Points
- **Main Access**: Emergency Report button on incidents index page
- **Direct URL**: `/driver/incidents/emergency`
- **From Dashboard**: Via incidents section

## Response Times
- **P1 (Critical)**: 5-15 minutes
- **P2 (High)**: 15-30 minutes  
- **P3 (Medium)**: 30-60 minutes
- **P4 (Low)**: Standard processing

## File Storage
- Photos are stored in `storage/app/public/incident-photos/`
- Maximum file size: 5MB per image
- Supported formats: JPG, PNG, GIF

## Integration
The system integrates seamlessly with the existing incident management system, creating standard incident records that can be viewed, assigned, and tracked through the normal workflow.