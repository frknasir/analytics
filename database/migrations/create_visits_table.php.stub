<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration
{
  /**
  * Run the migrations.
  */
  public function up()
  {
    Schema::create('visits', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('visitable_type');
      $table->string('visitable_id');
      $table->string('ip')->nullable();
      $table->string('agent')->nullable();
      $table->string('referer')->nullable();
      $table->string('user_id')->nullable();
      $table->timestamps();
    });
  }

  /**
  * Reverse the migrations.
  */
  public function down()
  {
    Schema::dropIfExists('visits');
  }
}