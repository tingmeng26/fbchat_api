<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReward extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('rewards', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name')->comment('獎項名稱');
      $table->integer('number')->comment('名額');
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
    Schema::dropIfExists('rewards');
  }
}
