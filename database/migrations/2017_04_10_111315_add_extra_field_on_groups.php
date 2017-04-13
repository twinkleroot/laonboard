<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldOnGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('subj_1')->nullable();
            $table->string('subj_2')->nullable();
            $table->string('subj_3')->nullable();
            $table->string('subj_4')->nullable();
            $table->string('subj_5')->nullable();
            $table->string('subj_6')->nullable();
            $table->string('subj_7')->nullable();
            $table->string('subj_8')->nullable();
            $table->string('subj_9')->nullable();
            $table->string('subj_10')->nullable();
            $table->string('value_1')->nullable();
            $table->string('value_2')->nullable();
            $table->string('value_3')->nullable();
            $table->string('value_4')->nullable();
            $table->string('value_5')->nullable();
            $table->string('value_6')->nullable();
            $table->string('value_7')->nullable();
            $table->string('value_8')->nullable();
            $table->string('value_9')->nullable();
            $table->string('value_10')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('subj_1');
            $table->dropColumn('subj_2');
            $table->dropColumn('subj_3');
            $table->dropColumn('subj_4');
            $table->dropColumn('subj_5');
            $table->dropColumn('subj_6');
            $table->dropColumn('subj_7');
            $table->dropColumn('subj_8');
            $table->dropColumn('subj_9');
            $table->dropColumn('subj_10');
            $table->dropColumn('value_1');
            $table->dropColumn('value_2');
            $table->dropColumn('value_3');
            $table->dropColumn('value_4');
            $table->dropColumn('value_5');
            $table->dropColumn('value_6');
            $table->dropColumn('value_7');
            $table->dropColumn('value_8');
            $table->dropColumn('value_9');
            $table->dropColumn('value_10');
        });
    }
}
