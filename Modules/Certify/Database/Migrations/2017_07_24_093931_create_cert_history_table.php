<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('cert_history')) {
            Schema::create('cert_history', function (Blueprint $table) {
                $table->increments('id');
                $table->string('user_email')->nullable();
                $table->string('company')->nullable();
                $table->string('method')->nullable();
                $table->string('ip')->nullable();
                $table->date('date')->nullable();
                $table->time('time')->nullable();
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
        if(Schema::hasTable('cert_history')) {
            Schema::dropIfExists('cert_history');
        }
    }
}
