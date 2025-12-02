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
    Schema::table('opportunities', function (Blueprint $table) {
        $table->date('expiry_date')->nullable()->after('location');
        $table->unsignedBigInteger('attachment_id')->nullable()->after('expiry_date');
    });
}

    /**
     * Reverse the migrations.
     */
    
public function down()
{
    Schema::table('opportunities', function (Blueprint $table) {
        $table->dropColumn(['expiry_date', 'attachment_id']);
    });
}
};
