<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressUserTable extends Migration
{
    public function up()
    {
        Schema::create('address_user', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['address_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('address_user');
    }
}