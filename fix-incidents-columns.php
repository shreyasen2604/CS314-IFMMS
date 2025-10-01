<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Fixing incidents table missing columns...\n";
echo "=========================================\n\n";

try {
    // Check current columns
    $currentColumns = Schema::getColumnListing('incidents');
    echo "Current columns in incidents table:\n";
    foreach ($currentColumns as $column) {
        echo "  - $column\n";
    }
    echo "\n";

    // Define columns that should exist
    $requiredColumns = [
        'location_description' => "ALTER TABLE incidents ADD COLUMN location_description TEXT NULL AFTER longitude",
        'fuel_level' => "ALTER TABLE incidents ADD COLUMN fuel_level VARCHAR(255) NULL AFTER odometer",
        'weather_conditions' => "ALTER TABLE incidents ADD COLUMN weather_conditions VARCHAR(255) NULL AFTER location_description",
        'road_conditions' => "ALTER TABLE incidents ADD COLUMN road_conditions VARCHAR(255) NULL AFTER weather_conditions",
        'additional_notes' => "ALTER TABLE incidents ADD COLUMN additional_notes TEXT NULL AFTER road_conditions"
    ];

    $addedColumns = [];
    $skippedColumns = [];

    foreach ($requiredColumns as $columnName => $sql) {
        if (!in_array($columnName, $currentColumns)) {
            try {
                DB::statement($sql);
                $addedColumns[] = $columnName;
                echo "✓ Added column: $columnName\n";
            } catch (\Exception $e) {
                // Try without AFTER clause if it fails
                $simpleSql = str_replace(
                    ["AFTER longitude", "AFTER odometer", "AFTER location_description", "AFTER weather_conditions", "AFTER road_conditions"],
                    "",
                    $sql
                );
                try {
                    DB::statement($simpleSql);
                    $addedColumns[] = $columnName;
                    echo "✓ Added column: $columnName (without position)\n";
                } catch (\Exception $e2) {
                    echo "✗ Failed to add column $columnName: " . $e2->getMessage() . "\n";
                }
            }
        } else {
            $skippedColumns[] = $columnName;
            echo "⊙ Column already exists: $columnName\n";
        }
    }

    echo "\n";
    echo "Summary:\n";
    echo "--------\n";
    echo "Added columns: " . (count($addedColumns) > 0 ? implode(', ', $addedColumns) : 'None') . "\n";
    echo "Skipped columns (already exist): " . (count($skippedColumns) > 0 ? implode(', ', $skippedColumns) : 'None') . "\n";

    // Verify final structure
    echo "\nFinal table structure:\n";
    $finalColumns = Schema::getColumnListing('incidents');
    foreach ($finalColumns as $column) {
        echo "  - $column\n";
    }

    echo "\n✅ Database fix completed successfully!\n";
    echo "You can now use the emergency incident reporting feature.\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
    exit(1);
}