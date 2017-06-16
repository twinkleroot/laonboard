<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_news', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('board_id')->unsigned();
            $table->integer('write_id')->unsigned();
            $table->integer('write_parent')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->integer('user_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_news');
    }
}
