<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadSalesEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_sales_employee', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('sales_user_id');
            $table->integer('lead_id')->nullable();
            $table->integer('active_status')->nullable();
            $table->integer('assign_by')->nullable();
            $table->integer('update_by')->nullable();
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
        Schema::table('lead_sales_employee', function (Blueprint $table) {
            //
        });
    }
}
