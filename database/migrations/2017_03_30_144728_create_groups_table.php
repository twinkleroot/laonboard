<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('group_id', 10);
                $table->string('subject');
                $table->enum('device', ['both', 'pc', 'mobile']);
                $table->string('admin')->nullable();
                $table->tinyInteger('use_access')->nullable();
                $table->integer('order')->default(0)->nullable();
                $table->timestamps();
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
