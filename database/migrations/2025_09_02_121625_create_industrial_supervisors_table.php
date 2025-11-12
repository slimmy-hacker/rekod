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
       Schema::create('industrial_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('company_id');
            $table->string('staff_number')->nullable();
            $table->string('position_title');
            $table->string('phone_alt')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();

           $table->unique(['staff_number', 'company_id'], 'company_staff_company_unique');

       });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industrial_supervisors');
    }
};
