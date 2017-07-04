<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->tinyInteger('use_recaptcha')->default(0);
            $table->dropColumn('include_head');
            $table->dropColumn('include_tail');
            $table->string('layout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn('use_recaptcha');
            $table->dropColumn('layout');
            $table->string('include_head')->nullable();
            $table->string('include_tail')->nullable();
        });
    }
}
