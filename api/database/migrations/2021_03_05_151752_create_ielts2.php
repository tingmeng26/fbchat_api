<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIelts2 extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ielts', function (Blueprint $table) {
      $table->increments('id');
      $table->bigInteger('fbid')->comment('fb id');
      $table->string('name', 100)->comment('fb name');
      $table->enum('a1', [1, 2, 3, 4])->comment('q1 answer');
      $table->enum('a2', [1, 2, 3, 4])->comment('q2 answer');
      $table->enum('a3', [1, 2, 3, 4])->comment('q3 answer');
      $table->enum('a4', [1, 2, 3, 4])->comment('q4 answer');
      $table->enum('a5', [1, 2, 3, 4])->comment('q5 answer');
      $table->enum('result', [1, 2, 3, 4])->comment('測驗結果');
      $table->string('email', 80)->comment('email');
      $table->enum('agree', [0, 1])->default(0)->comment('是否同意活動聲明');
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
    Schema::dropIfExists('ielts');
  }
}
