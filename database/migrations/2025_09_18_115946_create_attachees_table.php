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
        Schema::create('attachees', function (Blueprint $table) {
            $table->id('attachment_id');

            $table->string('student_regno');
            $table->string('lecturer_staffNumber');


            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('industrial_supervisor_id');

            $table->timestamps();


         //   $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
         //   $table->foreign('industrial_supervisor_id')->references('id')->on('industry_supervisors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachees');
    }
};
