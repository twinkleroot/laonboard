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
			return "<?php echo substr($expression, 0, 10); ?>";
		});

		// Blade Template 의 지시어 'monthAndDay' 생성
		Blade::directive('monthAndDay', function($expression) {
			return "<?php echo date('m/d', strtotime($expression)); ?>";
		});

		// Blade Template 의 지시어 'hourAndMin' 생성
		Blade::directive('hourAndMin', function($expression) {
			return "<?php echo date('H:i', strtotime($expression)); ?>";
		});

		// Blade Template 의 지시어 'datetime' 생성
		Blade::directive('datetime', function($expression) {
			return "<?php echo ($expression)->format('Y/m/d H:i'); ?>";
		});

		// nick name 유효성 검사 추가
		Validator::extend('nick_length', function($attribute, $value, $parameters, $validator){
			return strlen($value) >= $parameters[1];
		});

		// nick name 유효성 검사 결과 메세지의 :min 부분에 체크하려는 bytes 대입
		Validator::replacer('nick_length', function($message, $attribute, $rule, $parameters){
			return str_replace(array(':half', ':min'), $parameters, $message);
		});

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
