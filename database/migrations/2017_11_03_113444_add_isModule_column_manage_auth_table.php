<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsModuleColumnManageAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manage_auth', function (Blueprint $table) {
            $table->tinyInteger('isModule')->unsigned()->default('0');
            $table->string('menu', 50);
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
            //
        });
    }
}
