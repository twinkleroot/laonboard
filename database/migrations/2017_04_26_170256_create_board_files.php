<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_files', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('board_id');
            $table->integer('write_id');
            $table->string('source');
            $table->string('file');
            $table->integer('download');
            $table->text('content');
            $table->integer('filesize');
            $table->integer('width');
            $table->smallInteger('height');
            $table->tinyInteger('type');
            $table->dateTime('created_at');

            $table->primary(['id', 'board_id', 'write_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_files');
    }
}
