<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_details', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('user_id');
            $table->integer('document_certificate_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('work_location')->nullable();
            $table->string('lead_from')->nullable();
            $table->integer('star_review')->nullable();
            $table->timestamp('lead_apply_date')->nullable();
            $table->text('lead_remarks')->nullable();
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
        Schema::table('lead_details', function (Blueprint $table) {
            //
        });
    }
}
