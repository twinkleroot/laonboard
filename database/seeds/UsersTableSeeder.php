<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nowDate = Carbon::now()->toDateString();

        $admin = [
            'name' => '관리자',
            'nick' => '관리자',
            'nick_date' => $nowDate,
            'email' => config('gnu.superAdmin'),
            'password' => bcrypt('admin'),
            'level' => 10,
            'point' => 9999999,
            'mailing' => 1,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'email_certify' => Carbon::now(),
        ];

        User::insert($admin);
        $user = User::find(DB::getPdo()->lastInsertId());
        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));
        $user->save();
    }
}
