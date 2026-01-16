<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the table is gone before creating
        Schema::dropIfExists('weekly_reports');

        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            
            // Link directly to the students table
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            $table->integer('week_id'); // 1, 2, 3, etc.
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->text('weekly_report')->nullable();
            
            // Feedback columns
            $table->text('industrial_supervisor_comment')->nullable();
            $table->text('lecturer_comment')->nullable();
            
            // Approval status
            $table->boolean('is_approved')->default(false);

            // Constraint: One student cannot submit the same week twice
            $table->unique(['student_id', 'week_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};