<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMealTable extends Migration
{
    public function up()
    {
        Schema::create('order_meal', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('meal_id');
            $table->string('quantity');
            $table->enum('size', ['large', 'Medium ','small']);
            $table->text('addons')->nullable();
            $table->text('withouts')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
            $table->primary(['order_id', 'meal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_meal');
    }
}