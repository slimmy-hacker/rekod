<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Drop foreign key constraint first (if it exists)
            $table->dropForeign(['weekly_report_id']);
            // Then drop the column
            $table->dropColumn('weekly_report_id');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Add back the column
            $table->unsignedBigInteger('weekly_report_id')->nullable();
            // Re-add foreign key if needed
            // $table->foreign('weekly_report_id')->references('id')->on('weekly_reports');
        });
    }
};