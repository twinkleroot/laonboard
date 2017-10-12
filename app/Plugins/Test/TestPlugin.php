<?php

namespace App\Plugins\Test;

use Oneso\LaravelPlugins\Plugin;

class TestPlugin extends Plugin
{
	public $name;

	public function __construct()
	{
		$this->app = app();
		$this->name = 'test';
	}

	public function boot()
	{
		$this->enableViews();
		$this->enableRoutes();
	}
}
