1. www 아래에서 실행<br />
$ git clone git@github.com:gnuboard/laratest.git<br /><br />

2. 기본 탑재된 설정 파일을 복사<br />
$ cd laratest<br />
$ cp .env.example .env<br /><br />

3. 세션과 데이타 암호화에 사용하는 대칭키를 생성합니다. <br />
$ php artisan key:gen<br /><br />

4. .env 를 열어서 APP_DEBUG 와 APP_ENV 를 설정하며 그외 DB 연결 정보와 mailgun, github 인증 정보등을 사용자의 인증 정보에 맞게 수정합니다.<br />
APP_ENV=local<br />
APP_DEBUG=false<br />
APP_URL=http://laratest.gnutest.com/<br /><br />

NAVER, MAIL 은 일단 현재 개발에 쓰고 있는 걸로 설정합니다.<br />
NAVER는 개별적으로 네이버 개발자센터 https://developers.naver.com 에서 애플리케이션 등록하고 나온 값을 넣어줘야 합니다.<br /><br />

5. 데이타베이스 마이그레이션을 실행<br />
$ php artisan migrate<br /><br />

6. MySQL 버전이 5.7.9 미만인 경우(5.7.9부터는 기본 ROW__FORMAT 이 DYNAMIC 임)
unique 키, index 생성하는 부분에서 에러가 납니다. MySQL에서 <br /><br />

ALTER users ROW_FORMAT = DYNAMIC; <br />
ALTER password_resets ROW_FORMAT = DYNAMIC; <br /><br />

위와 같이 해준 후 다시 migrate 한다. <br />
$ php artisan migrate<br /><br />

7. 관리자 데이타를 생성<br />
$ php artisan db:seed --class=UsersTableSeeder<br />
$ php artisan db:seed --class=ConfigsTableSeeder<br /><br />

8. www 아래에서 심볼릭 링크 생성<br />
$ ln -s laratest/public public<br /><br />

9. laratest/public 경로에서 파일 업로드를 위한 심볼릭 링크 생성<br />
$ ln -s /home/ahn13/www/laratest/storage/app/public/ storage<br /><br />

<br />
## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
