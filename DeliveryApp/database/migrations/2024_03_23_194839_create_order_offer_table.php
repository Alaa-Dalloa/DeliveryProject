<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderOfferTable extends Migration
{
    public function up()
    {
        Schema::create('order_offer', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('offer_id');
            $table->string('quantity');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->primary(['order_id', 'offer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_offer');
    }
}