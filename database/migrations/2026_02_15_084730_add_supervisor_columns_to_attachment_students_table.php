<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('attachment_students', function (Blueprint $table) {
        // Adding the missing columns from the error message
        $table->string('industrial_supervisor')->nullable()->after('end_date');
        $table->string('supervisor_email')->nullable()->after('industrial_supervisor');
        $table->string('supervisor_phone')->nullable()->after('supervisor_email');
    });
}

public function down()
{
    Schema::table('attachment_students', function (Blueprint $table) {
        $table->dropColumn(['industrial_supervisor', 'supervisor_email', 'supervisor_phone']);
    });
}
};
