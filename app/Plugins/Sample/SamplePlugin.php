<?php

namespace App\Plugins\Sample;

use Oneso\LaravelPlugins\Plugin;

class SamplePlugin extends Plugin
{
	public $name;

	public function __construct()
	{
		$this->app = app();

		$this->name = 'sample';
	}

	public function boot()
	{
		$this->enableViews();
		$this->enableRoutes();


	}
}
