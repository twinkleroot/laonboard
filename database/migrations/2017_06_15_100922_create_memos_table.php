<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recv_user_id')->unsigned()->index();
            $table->integer('send_user_id')->unsigned();
            $table->timestamp('send_timestamp')->nullable();
            $table->timestamp('read_timestamp')->nullable();
            $table->text('memo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memos');
    }
}
