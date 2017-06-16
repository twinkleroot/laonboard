<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('board_id');
            $table->integer('write_id');
            $table->integer('user_id');
            $table->string('flag');
            $table->timestamp('created_at')->nullable();

            $table->unique(['board_id', 'write_id', 'user_id'], 'fkey1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_goods');
    }
}
