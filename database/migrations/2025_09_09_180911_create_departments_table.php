<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('school_code'); 
            $table->timestamps();

            
            $table->foreign('school_code')
                  ->references('code')
                  ->on('schools')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
