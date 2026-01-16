<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            
            // The foreign key column as requested
            $table->unsignedBigInteger('attachment_lecturer_id');
            
            // Summary Statistics
            $table->integer('total_students')->default(0);
            $table->integer('total_towns')->default(0);
            
            // Financials (using decimal for currency precision)
            $table->decimal('daily_rate_used', 12, 2);
            $table->decimal('total_subsistence', 12, 2);
            $table->decimal('total_transport', 12, 2);
            $table->decimal('grand_total', 12, 2);
            
            // Metadata for reporting
            $table->string('academic_year')->nullable(); 
            $table->string('semester')->nullable();

            $table->timestamps();

            // Foreign key constraint linking to the lecturers table
            $table->foreign('attachment_lecturer_id')
                  ->references('id')
                  ->on('lecturers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};