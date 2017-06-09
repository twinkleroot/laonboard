<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contents')) {
            Schema::create('contents', function (Blueprint $table) {
                $table->increments('id');
                $table->string('content_id', 20);
                $table->tinyInteger('html')->nullable()->default(0);
                $table->string('subject')->nullable();
                $table->longText('content')->nullable();
                $table->longText('mobile_content')->nullable();
                $table->string('skin')->nullable();
                $table->string('mobile_skin')->nullable();
                $table->tinyInteger('tag_filter_use')->nullable()->default(0);
                $table->integer('hit')->nullable();
                $table->string('include_head')->nullable();
                $table->string('include_tail')->nullable();
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
        if(Schema::hasTable('contents')) {
            Schema::dropIfExists('contents');
        }
    }
}
