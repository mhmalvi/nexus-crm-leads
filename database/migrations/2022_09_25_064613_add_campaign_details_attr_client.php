<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignDetailsAttrClient extends Migration
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
            $table->integer('campaign_id')->after('campaign_name')->nullable();
            $table->integer('client_id')->after('campaign_id')->nullable();
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
