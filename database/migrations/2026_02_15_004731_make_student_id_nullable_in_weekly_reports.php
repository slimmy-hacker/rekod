<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        // This tells MySQL it's okay if student_id is empty
        $table->unsignedBigInteger('student_id')->nullable()->change();
    });
}

public function down()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        $table->unsignedBigInteger('student_id')->nullable(false)->change();
    });
}
};
