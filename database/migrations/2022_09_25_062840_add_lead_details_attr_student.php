<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadDetailsAttrStudent extends Migration
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
            $table->integer('student_id')->after('lead_id')->nullable();
            $table->integer('client_id')->after('student_id')->nullable();
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
