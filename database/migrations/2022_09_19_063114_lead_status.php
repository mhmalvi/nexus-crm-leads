<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_status', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('lead_status')->default(1)->comment('0=suspend, 1= new, 2= skilled, 3= called, 4= paid, 5= verified, 6= completed, 7= cancel');
            $table->integer('lead_id');
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
        Schema::table('lead_status', function (Blueprint $table) {
            //
        });
    }
}
