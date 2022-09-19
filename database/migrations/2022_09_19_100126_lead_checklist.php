<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadChecklist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_checklist', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('document_id')->nullable();
            $table->integer('lead_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->text('title')->nullable();
            $table->integer('status')->nullable();
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
        Schema::create('lead_checklist', function (Blueprint $table) {
            //
        });
    }
}
