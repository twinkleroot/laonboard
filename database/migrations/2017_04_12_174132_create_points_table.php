<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('points')) {
            Schema::create('points', function (Blueprint $table) {
                $table->increments('id');
                // users 테이블에 대한 참조키
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->timestamp('datetime')->nullable();
                $table->string('content')->nullable();
                $table->integer('point')->nullable()->default(0);
                $table->integer('use_point')->nullable()->default(0);
                $table->tinyInteger('expired')->default(0);
                $table->date('expire_date')->nullable();
                $table->integer('user_point')->nullable()->default(0);
                $table->string('rel_table', 20)->nullable();
                $table->string('rel_email', 50)->nullable();
                $table->string('rel_action')->nullable();

                $table->index(['expire_date'], 'index2');
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
        Schema::dropIfExists('points');
    }
}
