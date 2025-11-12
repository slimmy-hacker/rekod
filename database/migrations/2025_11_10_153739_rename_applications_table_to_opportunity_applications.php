<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameApplicationsTableToOpportunityApplications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::rename('applications', 'opportunity_applications');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::rename('opportunity_applications', 'applications');
    }
}
