<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('populars')) {
            Schema::create('populars', function (Blueprint $table) {
                $table->increments('id');
                $table->string('word', 50);
                $table->date('date');
                $table->string('ip', 50);

                $table->unique(['word', 'date', 'ip'], 'index1');
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
        if(Schema::hasTable('populars')) {
            Schema::dropIfExists('populars');
        }
    }
}
