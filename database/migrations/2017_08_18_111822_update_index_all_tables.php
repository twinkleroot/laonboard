<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manage_auth', function (Blueprint $table) {
            $table->dropIndex('manage_auth_user_id_index');
            $table->dropIndex('manage_auth_menu_index');
            $table->unique(['user_id', 'menu'], 'ma_unique');
        });

        Schema::table('points', function (Blueprint $table) {
            $table->index(['expire_date'], 'index2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manage_auth', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('menu');
            $table->dropUnique('ma_unique');
        });

        Schema::table('points', function (Blueprint $table) {
            $table->dropIndex('index2');
        });
    }
}
