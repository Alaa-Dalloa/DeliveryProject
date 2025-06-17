<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo');
            $table->enum('type', ['meal', 'offer ']);
            $table->unsignedBigInteger('meal_id')->nullable();
            $table->foreign('meal_id')->references('id')->on('meals')->nullOnDelete();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->nullOnDelete();
            $table->unsignedBigInteger('owner_resturent_id');
            $table->foreign('owner_resturent_id')->references('id')->on('owner_resturents');
            $table->boolean('Agree')->default(1);
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
        Schema::dropIfExists('advertisements');
    }
}
