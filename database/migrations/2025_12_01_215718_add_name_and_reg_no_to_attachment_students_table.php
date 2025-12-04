<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameAndRegNoToAttachmentStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('attachment_students', function (Blueprint $table) {
            $table->string('name')->after('id');     // Adjust position if needed
        });
    }

    public function down()
    {
        Schema::table('attachment_students', function (Blueprint $table) {
            $table->dropColumn(['name']);
        });
    }
}
