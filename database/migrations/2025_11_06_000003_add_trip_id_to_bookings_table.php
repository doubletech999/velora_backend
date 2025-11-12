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
            // إزالة foreign key constraint الحالي لـ guide_id
            $table->dropForeign(['guide_id']);
            
            // جعل guide_id nullable
            $table->foreignId('guide_id')->nullable()->change();
            
            // إعادة إضافة foreign key constraint
            $table->foreign('guide_id')->references('id')->on('guides')->onDelete('set null');
            
            // إضافة trip_id
            $table->foreignId('trip_id')->nullable()->after('user_id')->constrained('trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // إزالة trip_id
            $table->dropForeign(['trip_id']);
            $table->dropColumn('trip_id');
            
            // إعادة guide_id كمطلوب
            $table->dropForeign(['guide_id']);
            $table->foreignId('guide_id')->nullable(false)->change();
            $table->foreign('guide_id')->references('id')->on('guides')->onDelete('cascade');
        });
    }
};