<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
public function up()
{
    Schema::table('attachment_students', function (Blueprint $table) {
        
        $table->unsignedBigInteger('attachment_lecturer_id')->nullable();
        
        
    });
}

public function down()
{
    Schema::table('attachment_students', function (Blueprint $table) {
        $table->dropColumn('attachment_lecturer_id');
    });
}
};
