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
        Schema::create('lecturer_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attachment_id')
                ->constrained('attachments')
                ->cascadeOnDelete();

            $table->integer('department_id');
//            $table->foreignId('d')
//                ->constrained()
//                ->cascadeOnDelete();

            $table->integer('batch');

            $table->foreignId('attachment_student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('attachment_lecturer_id')
                ->constrained('attachment_lecturers')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            // Geo coordinates (from company)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Draft / final state
            $table->boolean('is_final')->default(false);

            // Audit
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate student drafts per period
            $table->unique([
                'batch',
                'attachment_student_id'
            ], 'unique_student_attachment_draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_assignments');
    }
};
