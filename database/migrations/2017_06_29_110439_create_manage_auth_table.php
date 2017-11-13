<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateManageAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_auth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('menu', 50);
            $table->enum('auth', ['r', 'w', 'd']);
            $table->tinyInteger('isModule')->unsigned()->default('0');

            $table->unique(['user_id', 'menu'], 'ma_unique');
        });

        // 라라벨 기본 API에서 mysql의 set type을 지원하지 않으므로 enum으로 생성하고 set으로 변경한다.
        DB::statement("ALTER TABLE ". DB::getTablePrefix(). "manage_auth CHANGE `auth` `auth` SET('r', 'w', 'd');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manage_auth');
    }
}
