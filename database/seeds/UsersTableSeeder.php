<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
                'name' => '관리자',
                'nick' => '관리자',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin'),
                'level' => 10,
                'point' => 9999999,
                'mailing' => 1,
                'sms' => 1,
                'open' => 1,
        ];

        App\User::create($admin);
    }
}
