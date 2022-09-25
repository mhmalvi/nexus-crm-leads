<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CampaignDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_details', function (Blueprint $table) {
            //
            $table->id();
            $table->text('campaign_name')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('stop_time')->nullable();
            $table->integer('campaign_status')->nullable();
            $table->timestamps();
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
