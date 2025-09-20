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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->unsignedBigInteger('company_id'); // FK to companies table
            $table->string('name'); // Branch name (e.g., Nairobi Branch)

            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();

            $table->string('county');
            $table->string('sub_county')->nullable();
            $table->string('ward')->nullable();

            $table->timestamps();

            
            $table->foreign('company_id')
                  ->references('id')->on('companies')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
