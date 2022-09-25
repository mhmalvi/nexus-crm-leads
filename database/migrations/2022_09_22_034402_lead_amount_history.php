<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadAmountHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_amount_history', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id')->nullable();
            $table->float('amount')->nullable();
            $table->timestamps();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_amount_history', function (Blueprint $table) {
            //
        });
    }
}
