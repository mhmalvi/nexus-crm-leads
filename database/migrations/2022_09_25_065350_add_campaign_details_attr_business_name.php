<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignDetailsAttrBusinessName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_details', function (Blueprint $table) {
            //
            $table->integer('business_id')->after('client_id')->nullable();
            $table->string('business_name')->after('business_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_details', function (Blueprint $table) {
            //
        });
    }
}
