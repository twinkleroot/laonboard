<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
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
