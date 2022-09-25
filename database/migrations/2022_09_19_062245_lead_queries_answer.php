<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadQueriesAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_queries_answer', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('lead_id')->nullable();
            $table->text('lead_answer')->nullable();
            $table->integer('question_id')->nullable();
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
        Schema::table('lead_queries_answer', function (Blueprint $table) {
            //
        });
    }
}
