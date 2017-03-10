1. Git에서 Laratest Project를 Clone <br />
$ git clone git@github.com:gnuboard/laratest.git <br /><br />

2. DB 생성 <br />
$ php artisan migrate <br /><br />

3. MySQL 버전이 5.7.9 미만인 경우(5.7.9부터는 기본 ROW__FORMAT 이 DYNAMIC 임) <br />
unique 키, index 생성하는 부분에서 에러가 난다. <br />
mysql에서 <br /><br />

ALTER TABLE 테이블이름(users) ROW_FORMAT = DYNAMIC; <br />
ALTER TABLE 테이블이름(password_resets) ROW_FORMAT = DYNAMIC; <br /><br />

해준 후 다시 migrate 한다. <br /><br />

4. seed 데이터 생성(관리자 데이터 생성) <br />
$ php artisan db:seed <br />

5. 테스트 데이터 생성(Users) – 숫자는 원하는 데이터 개수만큼 넣는다. <br />
$ php artisan tinker <br />
\>\>\> factory(App\User::class, 10)->create(); <br />

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
