<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withouts', function (Blueprint $table) {
            $table->id();
            $table->string('without_name');
             $table->unsignedBigInteger('meal_id');
            $table->foreign('meal_id')->references('id')->on('meals');
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
        Schema::dropIfExists('withouts');
    }
}
