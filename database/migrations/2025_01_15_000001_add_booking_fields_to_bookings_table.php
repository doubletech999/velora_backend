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
        Schema::table('bookings', function (Blueprint $table) {
            // Add new fields
            $table->string('path_id')->nullable()->after('trip_id');
            $table->string('site_id')->nullable()->after('path_id');
            $table->integer('number_of_participants')->default(1)->after('total_price');
            $table->enum('payment_method', ['cash', 'visa'])->default('cash')->after('number_of_participants');
            
            // Update status enum to include 'rejected'
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])->default('pending')->change();
            
            // Add indexes
            $table->index('path_id');
            $table->index('site_id');
            $table->index('status');
            $table->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['path_id']);
            $table->dropIndex(['site_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['booking_date']);
            
            // Remove new fields
            $table->dropColumn(['path_id', 'site_id', 'number_of_participants', 'payment_method']);
            
            // Revert status enum
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending')->change();
        });
    }
};


