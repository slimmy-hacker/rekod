<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Add report_date column (nullable first to copy data)
            $table->date('report_date')->nullable()->after('id');
        });

        // Copy data from start_date to report_date (preserve existing data)
        DB::statement('UPDATE daily_reports SET report_date = start_date');

        // Now make report_date not nullable and drop old columns
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->date('report_date')->nullable(false)->change();
            
            // Drop the old columns
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Add back the old columns
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Copy data back
            DB::statement('UPDATE daily_reports SET start_date = report_date, end_date = report_date');
            
            // Drop the new column
            $table->dropColumn('report_date');
        });
    }
};