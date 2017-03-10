Git에서 Laratest Project를 Clone
$ git clone git@github.com:gnuboard/laratest.git

DB 생성
$ php artisan migrate

MySQL 버전이 5.7.9 미만인 경우(5.7.9부터는 기본 ROW__FORMAT 이 DYNAMIC 임) unique 키 생성하는 부분에서 에러가 난다.
mysql에서

ALTER TABLE 테이블이름(users) ROW_FORMAT = DYNAMIC; <br />
ALTER TABLE 테이블이름(password_resets) ROW_FORMAT = DYNAMIC;

해준 후 다시 migrate 한다.

seed 데이터 생성(관리자 데이터 생성)
$ php artisan db:seed

테스트 데이터 생성(Users) – 숫자는 원하는 데이터 개수만큼 넣는다.
$ php artisan tinker
\>\>\> factory(App\User::class, 10)->create();

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
