<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('daily_reports', 'weekly_report_id')) {
            // Column never existed on this database (e.g. fresh install) — nothing to do.
            return;
        }

        Schema::table('daily_reports', function (Blueprint $table) {
            // Only drop the foreign key if it actually exists, to avoid crashing
            // on databases where the column was added without the constraint.
            $foreignKeyExists = collect(DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = 'daily_reports'
                 AND COLUMN_NAME = 'weekly_report_id'
                 AND CONSTRAINT_NAME != 'PRIMARY'"
            ))->isNotEmpty();

            if ($foreignKeyExists) {
                $table->dropForeign(['weekly_report_id']);
            }

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