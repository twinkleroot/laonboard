<?php

use Illuminate\Database\Seeder;
use App\Admin\Config;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $config = new Config();
        // 홈페이지 기본환경 설정
        $config->createConfigHomepage();
        // 회원 가입 설정
        $config->createConfigJoin();
        // 게시판 기본 설정
        $config->createConfigBoard();
    }
}
