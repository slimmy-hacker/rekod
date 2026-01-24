<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('companies', function (Blueprint $table) {
        // Adding the missing column
        $table->unsignedBigInteger('town_id')->nullable()->after('county_id');
        
        // Optional: Add a foreign key constraint to keep data clean
        $table->foreign('town_id')->references('id')->on('locations')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropForeign(['town_id']);
        $table->dropColumn('town_id');
    });
}
};