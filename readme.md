1. www 아래에서 실행 <br />
$ git clone git@github.com:gnuboard/laratest.git <br /><br />

2. 기본 탑재된 설정 파일을 복사 <br />
$ cd laratest <br />
$ cp .env.example .env <br /><br />

3. 세션과 데이타 암호화에 사용하는 대칭키를 생성합니다. <br />
$ php artisan key:gen <br /><br />

4. $ vi.env 후 DB 연결 정보를 수정합니다. <br />

5. 데이타베이스 마이그레이션을 실행 <br />
$ php artisan migrate <br /><br />

6. 관리자 데이타를 생성 <br />
$ php artisan db:seed --class=UsersTableSeeder <br />
$ php artisan db:seed --class=ConfigsTableSeeder <br /><br />

7. www 아래에서 심볼릭 링크 생성 <br />
$ ln -s laratest/public public <br /><br />

8. laratest/public 경로에서 파일 업로드를 위한 심볼릭 링크 생성 <br />
$ ln -s /~www까지의 절대경로/www/laratest/storage/app/public/ storage <br /><br />

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
