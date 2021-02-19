<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMission extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('missions', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('m_id')->comment('member id');
      $table->tinyInteger('mission1')->default(0)->comment('mission1 1=完成');
      $table->tinyInteger('mission2')->default(0)->comment('mission2 1=完成');
      $table->tinyInteger('mission3')->default(0)->comment('mission3 1=完成');
      $table->tinyInteger('mission4')->default(0)->comment('mission4 1=完成');
      $table->tinyInteger('mission5')->default(0)->comment('mission5 1=完成');
      $table->tinyInteger('all_mission')->default(0)->comment('是否全部任務不含extra皆完成 1=完成');
      $table->tinyInteger('extra1')->default(0)->comment('彩蛋戳記1');
      $table->tinyInteger('extra2')->default(0)->comment('彩蛋戳記2');
      $table->tinyInteger('extra3')->default(0)->comment('彩蛋戳記3');
      $table->tinyInteger('extra4')->default(0)->comment('彩蛋戳記4');
      $table->tinyInteger('all_extra')->default(0)->comment('全部彩蛋戳記');
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
    Schema::dropIfExists('missions');
  }
}
