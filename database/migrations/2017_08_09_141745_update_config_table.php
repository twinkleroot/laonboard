<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Admin\Config;

class UpdateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        cache()->forget('config.email.default');
        cache()->forget('config.sns');
        cache()->forget('config.extra');
        Config::where('name', 'config.email.default')->delete();
        Config::where('name', 'config.sns')->delete();
        Config::where('name', 'config.extra')->delete();
        Artisan::call('config:clear', []);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
