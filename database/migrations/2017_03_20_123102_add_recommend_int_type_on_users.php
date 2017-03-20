<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecommendIntTypeOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('users','recommend')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('recommend', false, true)->nullable()->after('signature');
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
        if(Schema::hasColumn('users','recommend')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('recommend');
            });
        }
    }
}
