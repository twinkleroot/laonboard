<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Config;
use DB;

class ConfigController extends Controller
{
    public function index()
    {
        $config = Config::where([
            'name' => 'config.join',
        ])->first();

        if(is_null($config)) {
            $configArr = array (
              'nickDate' => config('gnu.nickDate'),
              'openDate' => config('gnu.openDate'),
              'name' => config('gnu.name'),
              'homepage' => config('gnu.homepage'),
              'tel' => config('gnu.tel'),
              'hp' => config('gnu.hp'),
              'addr' => config('gnu.addr'),
              'signature' => config('gnu.signature'),
              'profile' => config('gnu.profile'),
              'recommend' => config('gnu.recommend'),
              'joinLevel' => config('gnu.joinLevel'),
              'joinPoint' => config('gnu.joinPoint'),
              'recommendPoint' => config('gnu.recommendPoint'),
              'loginPoint' => config('gnu.loginPoint'),
              'banId' => config('gnu.banId'),
              'stipulation' => config('gnu.stipulation'),
              'privacy' => config('gnu.privacy'),
              'password_policy_digits' => config('gnu.password_policy_digits'),
              'password_policy_special' => config('gnu.password_policy_special'),
              'password_policy_upper' => config('gnu.password_policy_upper'),
              'password_policy_number' => config('gnu.password_policy_number'),
            );

            DB::table('configs')->insert([
                'name' => 'config.join',
                'vars' => json_encode($configArr)
            ]);

            $config = Config::where([
                'name' => 'config.join',
            ])->first();
        }

        return view('admin.config.index')->with('config', json_decode($config->vars));
    }

    public function update(Request $request)
    {
        $config = Config::where([
            'name' => 'config.join',
        ])->first();

        $configArr = array (
          'nickDate' => $request->get('nickDate'),
          'openDate' => $request->get('openDate'),
          'name' => $request->get('name'),
          'homepage' => $request->get('homepage'),
          'tel' => $request->get('tel'),
          'hp' => $request->get('hp'),
          'addr' => $request->get('addr'),
          'signature' => $request->get('signature'),
          'profile' => $request->get('profile'),
          'recommend' => $request->get('recommend'),
          'joinLevel' => $request->get('joinLevel'),
          'joinPoint' => $request->get('joinPoint'),
          'recommendPoint' => $request->get('recommendPoint'),
          'loginPoint' => $request->get('loginPoint'),
          'banId' => array (
            0 => $request->get('banId'),
          ),
          'stipulation' => $request->get('stipulation'),
          'privacy' => $request->get('privacy'),
          'password_policy_digits' => $request->get('password_policy_digits'),
          'password_policy_special' => $request->get('password_policy_special'),
          'password_policy_upper' => $request->get('password_policy_upper'),
          'password_policy_number' => $request->get('password_policy_number'),
        );

        $config->vars = json_encode($configArr);
        $config->save();

        return redirect(route('admin.config'))->with('message', '환경 설정 변경이 완료되었습니다.');
    }
}
