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
            $table->integer('board_id');
            $table->integer('write_id');
            $table->integer('board_file_no');
            $table->string('source');
            $table->string('file');
            $table->integer('download')->default(0);
            $table->text('content')->nullable();
            $table->integer('filesize');
            $table->integer('width')->default(0);
            $table->smallInteger('height')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->primary(['board_id', 'write_id', 'board_file_no']);
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
