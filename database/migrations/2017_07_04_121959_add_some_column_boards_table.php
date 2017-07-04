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
            $table->renameColumn('include_head', 'layout');
            $table->dropColumn('include_tail');
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
            $table->renameColumn('layout', 'include_head');
            $table->string('include_tail')->nullable();
        });
    }
}
