<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('group_user')) {
            Schema::create('group_user', function (Blueprint $table) {
                $table->increments('id');
                // users 테이블에 대한 참조키
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                // groups 테이블에 대한 참조키
                $table->integer('group_id')->unsigned();
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
                $table->dateTime('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_user');
    }
}
