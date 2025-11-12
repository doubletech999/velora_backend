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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->unique()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                // Drop the unique index if it exists, then drop the column
                try {
                    $table->dropUnique('users_phone_unique');
                } catch (\Throwable $e) {
                    // ignore if index name differs or doesn't exist
                }
                $table->dropColumn('phone');
            }
        });
    }
};
