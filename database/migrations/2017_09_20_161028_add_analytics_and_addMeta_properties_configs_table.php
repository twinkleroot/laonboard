<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use App\Config;

class AddAnalyticsAndAddMetaPropertiesConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!isset(cache('config.homepage')->analytics) && !isset(cache('config.homepage')->addMeta)) {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            $homepageConfig = Config::whereName('config.homepage')->first();
            $configs = json_decode($homepageConfig->vars, true);

            $configs = array_add($configs, 'analytics', config('gnu.analytics'));
            $configs = array_add($configs, 'addMeta', config('gnu.addMeta'));

            $homepageConfig->vars = json_encode($configs);

            $homepageConfig->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(isset(cache('config.homepage')->analytics) && isset(cache('config.homepage')->addMeta)) {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            $homepageConfig = Config::whereName('config.homepage')->first();
            $configs = json_decode($homepageConfig->vars, true);

            $configs = array_except($configs, ['analytics', 'addMeta']);

            $homepageConfig->vars = json_encode($configs);

            $homepageConfig->save();
        }
    }
}
