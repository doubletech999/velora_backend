<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Common fields that might be missing
            if (!Schema::hasColumn('sites', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('sites', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
            if (!Schema::hasColumn('sites', 'location')) {
                $table->string('location')->nullable()->after('description_ar');
            }
            if (!Schema::hasColumn('sites', 'location_ar')) {
                $table->string('location_ar')->nullable()->after('location');
            }
            if (!Schema::hasColumn('sites', 'images')) {
                $table->json('images')->nullable()->after('image_url');
            }
            
            // Route/Camping specific fields
            if (!Schema::hasColumn('sites', 'length')) {
                $table->decimal('length', 8, 2)->nullable()->comment('Distance in kilometers')->after('distance');
            }
            if (!Schema::hasColumn('sites', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->comment('Duration in hours')->after('duration');
            }
            if (!Schema::hasColumn('sites', 'difficulty')) {
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable()->after('estimated_duration');
            }
            if (!Schema::hasColumn('sites', 'warnings')) {
                $table->json('warnings')->nullable()->after('activities');
            }
            if (!Schema::hasColumn('sites', 'warnings_ar')) {
                $table->json('warnings_ar')->nullable()->after('warnings');
            }
            if (!Schema::hasColumn('sites', 'coordinates')) {
                $table->json('coordinates')->nullable()->comment('Array of {latitude, longitude} objects')->after('warnings_ar');
            }
            
            // Hotel specific fields
            if (!Schema::hasColumn('sites', 'star_rating')) {
                $table->tinyInteger('star_rating')->nullable()->comment('1-5 stars')->after('coordinates');
            }
            if (!Schema::hasColumn('sites', 'price_per_night')) {
                $table->decimal('price_per_night', 10, 2)->nullable()->after('star_rating');
            }
            if (!Schema::hasColumn('sites', 'hotel_amenities')) {
                $table->json('hotel_amenities')->nullable()->after('price_per_night');
            }
            if (!Schema::hasColumn('sites', 'hotel_amenities_ar')) {
                $table->json('hotel_amenities_ar')->nullable()->after('hotel_amenities');
            }
            if (!Schema::hasColumn('sites', 'room_count')) {
                $table->integer('room_count')->nullable()->after('hotel_amenities_ar');
            }
            if (!Schema::hasColumn('sites', 'check_in_time')) {
                $table->time('check_in_time')->nullable()->after('room_count');
            }
            if (!Schema::hasColumn('sites', 'check_out_time')) {
                $table->time('check_out_time')->nullable()->after('check_in_time');
            }
            
            // Restaurant specific fields
            if (!Schema::hasColumn('sites', 'cuisine_type')) {
                $table->string('cuisine_type', 100)->nullable()->after('check_out_time');
            }
            if (!Schema::hasColumn('sites', 'cuisine_type_ar')) {
                $table->string('cuisine_type_ar', 100)->nullable()->after('cuisine_type');
            }
            if (!Schema::hasColumn('sites', 'average_price')) {
                $table->decimal('average_price', 10, 2)->nullable()->after('cuisine_type_ar');
            }
            if (!Schema::hasColumn('sites', 'price_range')) {
                $table->string('price_range', 10)->nullable()->comment('$, $$, $$$, $$$$')->after('average_price');
            }
            if (!Schema::hasColumn('sites', 'opening_hours')) {
                $table->json('opening_hours')->nullable()->comment('Object with day:time pairs')->after('price_range');
            }
            if (!Schema::hasColumn('sites', 'opening_hours_ar')) {
                $table->json('opening_hours_ar')->nullable()->after('opening_hours');
            }
            if (!Schema::hasColumn('sites', 'menu_url')) {
                $table->string('menu_url', 500)->nullable()->after('opening_hours_ar');
            }
            
            // Tourist site specific fields
            if (!Schema::hasColumn('sites', 'historical_period')) {
                $table->string('historical_period', 100)->nullable()->after('menu_url');
            }
            if (!Schema::hasColumn('sites', 'historical_period_ar')) {
                $table->string('historical_period_ar', 100)->nullable()->after('historical_period');
            }
            if (!Schema::hasColumn('sites', 'entrance_fee')) {
                $table->decimal('entrance_fee', 10, 2)->nullable()->after('historical_period_ar');
            }
            if (!Schema::hasColumn('sites', 'best_time_to_visit')) {
                $table->string('best_time_to_visit', 50)->nullable()->after('entrance_fee');
            }
            if (!Schema::hasColumn('sites', 'best_time_to_visit_ar')) {
                $table->string('best_time_to_visit_ar', 50)->nullable()->after('best_time_to_visit');
            }
            
            // Camping specific fields
            if (!Schema::hasColumn('sites', 'camping_amenities')) {
                $table->json('camping_amenities')->nullable()->after('best_time_to_visit_ar');
            }
            if (!Schema::hasColumn('sites', 'camping_amenities_ar')) {
                $table->json('camping_amenities_ar')->nullable()->after('camping_amenities');
            }
            if (!Schema::hasColumn('sites', 'capacity')) {
                $table->integer('capacity')->nullable()->comment('Number of people')->after('camping_amenities_ar');
            }
            
            // Common rating fields (if not exists)
            if (!Schema::hasColumn('sites', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.0)->after('capacity');
            }
            if (!Schema::hasColumn('sites', 'review_count')) {
                $table->integer('review_count')->default(0)->after('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $columnsToDrop = [
                'name_ar',
                'description_ar',
                'location',
                'location_ar',
                'images',
                'length',
                'estimated_duration',
                'difficulty',
                'warnings',
                'warnings_ar',
                'coordinates',
                'star_rating',
                'price_per_night',
                'hotel_amenities',
                'hotel_amenities_ar',
                'room_count',
                'check_in_time',
                'check_out_time',
                'cuisine_type',
                'cuisine_type_ar',
                'average_price',
                'price_range',
                'opening_hours',
                'opening_hours_ar',
                'menu_url',
                'historical_period',
                'historical_period_ar',
                'entrance_fee',
                'best_time_to_visit',
                'best_time_to_visit_ar',
                'camping_amenities',
                'camping_amenities_ar',
                'capacity',
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('sites', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

