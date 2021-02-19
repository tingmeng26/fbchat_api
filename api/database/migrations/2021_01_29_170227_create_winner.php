<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinner extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('winners', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('r_id')->comment('fk reward id');
      $table->integer('m_id')->unique()->comment('fk member id');
      $table->text('remark')->comment('備註');
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
    Schema::dropIfExists('winners');
  }
}
