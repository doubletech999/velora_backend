<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update type column to support all types: site, hotel, restaurant, route, camping
        try {
            $columnInfo = DB::select("SHOW COLUMNS FROM sites WHERE Field = 'type'");
            
            if (!empty($columnInfo)) {
                $columnType = $columnInfo[0]->Type;
                
                // If type is enum, update it
                if (stripos($columnType, 'enum') !== false) {
                    // Change enum to support all types
                    DB::statement("ALTER TABLE sites MODIFY COLUMN type ENUM('site', 'hotel', 'restaurant', 'route', 'camping') DEFAULT 'site'");
                } else {
                    // If it's string, we can add a check constraint or just leave it as is
                    // For now, we'll just ensure the column accepts these values
                }
            }
        } catch (\Exception $e) {
            // If it fails, try to alter the column directly
            try {
                DB::statement("ALTER TABLE sites MODIFY COLUMN type ENUM('site', 'hotel', 'restaurant', 'route', 'camping') DEFAULT 'site'");
            } catch (\Exception $e2) {
                // If enum doesn't work, change to varchar with validation at application level
                DB::statement("ALTER TABLE sites MODIFY COLUMN type VARCHAR(20) DEFAULT 'site'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum values
        try {
            DB::statement("ALTER TABLE sites MODIFY COLUMN type ENUM('site', 'hotel', 'restaurant') DEFAULT 'site'");
        } catch (\Exception $e) {
            // Ignore error
        }
    }
};

