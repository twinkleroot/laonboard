<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('popups')) {
            Schema::create('popups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('device', 10)->default('both')->nullable();
                $table->timestamp('begin_time')->default(null)->nullable();
                $table->timestamp('end_time')->default(null)->nullable();
                $table->integer('disable_hours')->default(0)->nullable();
                $table->integer('left')->default(0)->nullable();
                $table->integer('top')->default(0)->nullable();
                $table->integer('height')->default(0)->nullable();
                $table->integer('width')->default(0)->nullable();
                $table->string('color')->nullable()->default('#000000');
                $table->string('color_button')->nullable()->default('#393939');
                $table->string('color_button_font')->nullable()->default('#ffffff');
                $table->text('subject')->nullable();
                $table->text('content')->nullable();
                $table->tinyInteger('content_html')->default(0)->nullable();
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
        if(Schema::hasTable('popups')) {
            Schema::dropIfExists('popups');
        }
    }

}
