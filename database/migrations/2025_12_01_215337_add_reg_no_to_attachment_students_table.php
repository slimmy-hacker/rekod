<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegNoToAttachmentStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('attachment_students', function (Blueprint $table) {
            $table->string('reg_no')->after('student_id')->nullable()->unique();
            // Adjust type (string) and constraints as needed
        });
    }

    public function down()
    {
        Schema::table('attachment_students', function (Blueprint $table) {
            $table->dropColumn('reg_no');
        });
    }
}
