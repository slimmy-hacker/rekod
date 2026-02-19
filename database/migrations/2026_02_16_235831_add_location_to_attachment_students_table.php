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
    Schema::table('attachment_students', function (Blueprint $table) {
        $table->unsignedBigInteger('town_id')->nullable()->after('company_id');
        $table->unsignedBigInteger('county_id')->nullable()->after('town_id');

        // Optional: add foreign keys if you want
        $table->foreign('town_id')->references('id')->on('locations')->onDelete('set null');
        $table->foreign('county_id')->references('id')->on('locations')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('attachment_students', function (Blueprint $table) {
        $table->dropForeign(['town_id']);
        $table->dropForeign(['county_id']);
        $table->dropColumn(['town_id', 'county_id']);
    });
}
};