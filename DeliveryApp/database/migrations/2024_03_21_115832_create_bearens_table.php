<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBearensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bearens', function (Blueprint $table) {
            $table->id();
            $table->string('daily_sale_date');
            $table->string('total_sales');
            $table->string('total_delivery_cost');
            $table->string('total_summation');
            $table->unsignedBigInteger('owner_resturent_id');
            $table->foreign('owner_resturent_id')->references('id')->on('owner_resturents');
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
        Schema::dropIfExists('bearens');
    }
}
