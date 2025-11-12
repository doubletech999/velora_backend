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
            $table->decimal('price', 10, 2)->nullable()->after('type');
            $table->string('guide_name')->nullable()->after('price');
            $table->foreignId('guide_id')->nullable()->constrained('guides')->onDelete('set null')->after('guide_name');
            $table->decimal('distance', 8, 2)->nullable()->comment('Distance in kilometers')->after('guide_id');
            $table->string('duration')->nullable()->comment('Duration in format: "2 hours" or "1 day"')->after('distance');
            $table->json('activities')->nullable()->comment('Available activities')->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['guide_id']);
            $table->dropColumn(['price', 'guide_name', 'guide_id', 'distance', 'duration', 'activities']);
        });
    }
};