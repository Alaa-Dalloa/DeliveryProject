<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferMealTable extends Migration
{
    public function up()
    {
         Schema::create('offer_meal', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('meal_id');
            $table->foreign('offer_id')->references('id')->on('offers');
            $table->foreign('meal_id')->references('id')->on('meals');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offer_meal');
    }
}