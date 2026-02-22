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
        Schema::table('daily_reports', function (Blueprint $table) {
            // Add student_id column (nullable if existing records don't have it)
            $table->unsignedBigInteger('student_id')->nullable()->after('id');
            
            // Add foreign key constraint (optional but recommended)
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['student_id']);
            // Then drop the column
            $table->dropColumn('student_id');
        });
    }
};