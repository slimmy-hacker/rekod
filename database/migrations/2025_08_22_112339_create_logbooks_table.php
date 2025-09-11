<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
   Schema::create('logbooks', function (Blueprint $table) {
    $table->id();
    
    // Link to student_profiles table (so we can use registration_number indirectly)
    $table->foreignId('student_profile_id')
          ->constrained('student_profiles')
          ->cascadeOnDelete();

    $table->integer('week_number');
    $table->text('tasks_performed');
    $table->text('skills_learned')->nullable();
    $table->text('challenges')->nullable();
    $table->timestamps();
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
