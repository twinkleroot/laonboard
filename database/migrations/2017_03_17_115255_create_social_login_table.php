<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('social_logins')) {
            Schema::create('social_logins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('provider', 20)->nullable();
                $table->string('social_id')->nullable();
                $table->text('social_token')->nullable();
                $table->string('ip')->nullable();
                $table->timestamps();

                // users 테이블에 대한 참조키
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('social_logins', function (Blueprint $table) {
            $table->dropIfExists('social_logins');
        });
    }
}
