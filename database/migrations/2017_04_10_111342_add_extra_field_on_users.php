<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('extra_1')->nullable();
            $table->string('extra_2')->nullable();
            $table->string('extra_3')->nullable();
            $table->string('extra_4')->nullable();
            $table->string('extra_5')->nullable();
            $table->string('extra_6')->nullable();
            $table->string('extra_7')->nullable();
            $table->string('extra_8')->nullable();
            $table->string('extra_9')->nullable();
            $table->string('extra_10')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('extra_1');
            $table->dropColumn('extra_2');
            $table->dropColumn('extra_3');
            $table->dropColumn('extra_4');
            $table->dropColumn('extra_5');
            $table->dropColumn('extra_6');
            $table->dropColumn('extra_7');
            $table->dropColumn('extra_8');
            $table->dropColumn('extra_9');
            $table->dropColumn('extra_10');
        });
    }
}
