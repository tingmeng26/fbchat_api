<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('s_id')->index('s_id');
            $table->string('account')->index('account');
            $table->string('person_id',10)->comment('身分證');
            $table->integer('register');
            $table->string('access_token')->index('access_token');
            $table->string('refresh_token');
            $table->integer('is_public')->default(1);
            $table->timestamp('access_token_expire_time')->nullable();
            $table->timestamp('refresh_token_expire_time')->nullable();
            $table->string('admin',30);
            $table->date('expire_at')->comment('於第一個任務完成後算三天為該帳號有效期')->nullable();
            $table->timestamp('register_at')->comment('報到時間')->nullable();
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
        Schema::dropIfExists('members');
    }
}
