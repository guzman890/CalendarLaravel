<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Activity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->date('startDate');
            $table->date('deadline');
            $table->date('endDate');
            $table->string('title');
            $table->string('description');
            $table->integer('status');
            $table->bigInteger('owner')->unsigned();  
            $table->foreign('owner')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
