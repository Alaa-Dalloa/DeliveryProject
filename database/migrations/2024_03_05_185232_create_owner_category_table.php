<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnerCategoryTable extends Migration
{
    public function up()
    {
         Schema::create('owner_category', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('owner_resturent_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('owner_resturent_id')->references('id')->on('owner_resturents');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('owner_category');
    }
}