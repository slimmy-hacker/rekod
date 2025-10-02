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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // Company Name
            $table->string('alias');      // Company Alias
            $table->string('branch');     // Company Branch
            $table->string('address');    // Address
            $table->string('contact');    // Contact
            $table->string('county');     // County (dropdown)
            $table->string('subcounty')->nullable();
            $table->string('ward')->nullable(); // Subcounty
            $table->decimal('latitude', 10, 7)->nullable();   // Latitude
            $table->decimal('longitude', 10, 7)->nullable();  // Longitude
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
