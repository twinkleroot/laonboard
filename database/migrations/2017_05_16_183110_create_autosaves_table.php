<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutosavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autosaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('unique_id', 50);
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->datetime('created_at');

            $table->index('user_id');
            $table->unique('unique_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autosaves');
    }
}
