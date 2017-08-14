<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Admin\Config;

class UpdateConfigRecaptcha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sns = Config::where('name', 'config.sns')->first();
        if($sns) {
            Cache::forget('config.sns');
            Config::where('name', 'config.sns')->delete();
        }
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
