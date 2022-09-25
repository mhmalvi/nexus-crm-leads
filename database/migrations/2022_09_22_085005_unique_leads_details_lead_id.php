<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UniqueLeadsDetailsLeadId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_details', function (Blueprint $table) {
            //
            $table->integer('lead_id')->nullable(false)->change();

            $table->unique('lead_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_details', function (Blueprint $table) {
            //
        });
    }
}
