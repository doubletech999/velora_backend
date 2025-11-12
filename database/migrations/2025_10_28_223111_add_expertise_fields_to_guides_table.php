<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guides', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('guides', 'specializations')) {
                $table->string('specializations', 500)->nullable()->after('languages');
            }
            if (!Schema::hasColumn('guides', 'certifications')) {
                $table->text('certifications')->nullable()->after('specializations');
            }
            if (!Schema::hasColumn('guides', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('certifications');
            }
        });
    }

    public function down()
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn(['specializations', 'certifications', 'experience_years']);
        });
    }
};