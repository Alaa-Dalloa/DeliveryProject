<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnerOrderTable extends Migration
{
    public function up()
    {
         Schema::create('owner_order', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('owner_resturent_id');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('owner_resturent_id')->references('id')->on('owner_resturents');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('owner_order');
    }
}