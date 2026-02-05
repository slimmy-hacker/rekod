<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('opportunity_applications', function (Blueprint $table) {
        // We add user_id and ensure it's placed after the ID or opportunity_id
        $table->foreignId('user_id')
              ->after('opportunity_id') 
              ->constrained()
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('opportunity_applications', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
