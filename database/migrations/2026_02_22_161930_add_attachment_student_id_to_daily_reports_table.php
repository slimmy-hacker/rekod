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
            // Add attachment_student_id column (nullable since existing records may not have it)
            $table->unsignedBigInteger('attachment_student_id')->nullable()->after('id');
            
            // Add foreign key constraint
            $table->foreign('attachment_student_id')
                  ->references('id')
                  ->on('attachment_students')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['attachment_student_id']);
            // Then drop the column
            $table->dropColumn('attachment_student_id');
        });
    }
};