<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('visits')) {
            Schema::create('visits', function (Blueprint $table) {
                $table->increments('id');
                $table->string('ip', 50);
                $table->date('date');
                $table->time('time');
                $table->text('referer')->nullable();
                $table->string('agent')->nullable();
                $table->string('browser')->nullable();
                $table->string('os')->nullable();
                $table->string('device')->nullable();

                $table->unique(['ip', 'date'], 'index1');
                $table->index(['date'], 'index2');
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
        if(Schema::hasTable('visits')) {
            Schema::dropIfExists('visits');
        }
    }
}
