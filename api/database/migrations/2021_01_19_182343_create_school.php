<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city_code',2)->comment('縣市代碼');
            $table->string('city_name')->comment('縣市名稱');
            $table->string('school_code',6)->comment('學校代碼');
            $table->string('school_name')->comment('學校名稱');
            $table->string('school_type')->comment('e=>國小 j=>國中 o=>其他');
            $table->integer('number')->comment('學生數');
            $table->string('check_code',3)->comment('檢核碼');
            $table->string('admin',20)->comment('最後管理者');
            $table->integer('is_public')->comment('是否啟用')->default(1);
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
        Schema::dropIfExists('school');
    }
}
