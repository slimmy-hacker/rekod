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
        Schema::create('attachment_students', function (Blueprint $table) {
            $table->id();
            $table->string('reg_no');
            $table->string('attachment_slug');
            $table->string('department_slug');
            $table->string('company_branch_slug')->nullable();
            $table->string('school_supervisor_staff_no')->nullable();
            $table->string('industrial_supervisor_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_students');
    }
};
