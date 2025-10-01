-- Add missing columns to incidents table
-- Run this script in your MySQL database to fix the missing columns issue

-- Check and add location_description column
ALTER TABLE incidents 
ADD COLUMN IF NOT EXISTS location_description TEXT NULL AFTER longitude;

-- Check and add fuel_level column
ALTER TABLE incidents 
ADD COLUMN IF NOT EXISTS fuel_level VARCHAR(255) NULL AFTER odometer;

-- Check and add weather_conditions column
ALTER TABLE incidents 
ADD COLUMN IF NOT EXISTS weather_conditions VARCHAR(255) NULL AFTER location_description;

-- Check and add road_conditions column
ALTER TABLE incidents 
ADD COLUMN IF NOT EXISTS road_conditions VARCHAR(255) NULL AFTER weather_conditions;

-- Check and add additional_notes column
ALTER TABLE incidents 
ADD COLUMN IF NOT EXISTS additional_notes TEXT NULL AFTER road_conditions;

-- Display the updated table structure
DESCRIBE incidents;