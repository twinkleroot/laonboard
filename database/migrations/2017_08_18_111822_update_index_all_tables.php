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
            $table->unique(['user_id', 'menu'], 'mkey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
