<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Blade Template 의 지시어 'date' 생성
        Blade::directive('date', function($expression) {
            if($expression) {
                return "<?php echo substr($expression, 0, 10); ?>";
            } else {
                return $expression;
            }
        });

        // Blade Template 의 지시어 'monthAndDay' 생성
        Blade::directive('monthAndDay', function($expression) {
            if($expression) {
                return "<?php echo date('m/d', strtotime($expression)); ?>";
            } else {
                return $expression;
            }
        });

        // Blade Template 의 지시어 'hourAndMin' 생성
        Blade::directive('hourAndMin', function($expression) {
            if($expression) {
                return "<?php echo date('H:i', strtotime($expression)); ?>";
            } else {
                return $expression;
            }
        });

        // Blade Template 의 지시어 'datetime' 생성
        Blade::directive('datetime', function($expression) {
            if($expression) {
                return "<?php echo ($expression)->format('Y/m/d H:i'); ?>";
            } else {
                return $expression;
            }
        });

        // nick name 유효성 검사 추가
        Validator::extend('nick_length', function($attribute, $value, $parameters, $validator){
            return strlen($value) >= $parameters[1];
        });

        // nick name 유효성 검사 결과 메세지의 :min 부분에 체크하려는 bytes 대입
        Validator::replacer('nick_length', function($message, $attribute, $rule, $parameters){
            return str_replace(array(':half', ':min'), $parameters, $message);
        });

        // 인터페이스와 구현 객체 바인딩, 앱 내에서 사용할 수 있도록 태그 걸기
        $this->bindObject();

        // 내부 모듈 이벤트 앱 설정에 추가
        $this->mergeEventConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    // 인터페이스와 구현 객체 바인딩, 앱 내에서 사용할 수 있도록 태그 걸기
    private function bindObject()
    {
        // BoardInterface에 Board 구현체를 의존 주입
        $this->app->bind(
            'App\Contracts\BoardInterface',
            'App\Models\Board'
        );
        // WriteInterface에 Write 구현체를 의존 주입
        $this->app->bind(
            'App\Contracts\WriteInterface',
            'App\Models\Write'
        );

        // BoardInterface를 'board'로 태그
        $this->app->tag('App\Contracts\BoardInterface', 'board');
        // WriteInterface를 'write'로 태그
        $this->app->tag('App\Contracts\WriteInterface', 'write');
    }

    // 내부 모듈 이벤트 앱 설정에 추가
    private function mergeEventConfig()
    {
        mergeEvent(
            __DIR__.'/../Modules/Config/event.php', 'event'
        );
    }

}
