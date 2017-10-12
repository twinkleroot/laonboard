# laravel-plugins
Plugin system for Laravel 5.x and Lumen 5.x.
Plugins can overwrite and extend each other.

## Usage
For Lumen add to bootstrap/app.php:
`$app->register(\Oneso\LaravelPlugins\PluginServiceProvider::class);`

For Laravel add to 'providers' array in config/app.php:
`\Oneso\LaravelPlugins\PluginServiceProvider::class,`

## Create a plugin

### Structure
Plugins must be in app/Plugins. Example plugin structure:
- Test
  - Http
    - Controllers
      - TestController.php
  - src
    - views
      - test.blade.php
    - routes.php
  - TestPlugin.php

The TestPlugin class must extend the Oneso\LaravelPlugins\Plugin class, containing a unique $name property and a boot() method.

### Views
In the boot() method of your plugin call `$this->enableViews()`.
Optional you can pass a relative path to the views directory, default to `src/views`.
Views automatically have a namespace (`"plugin:{plugin name}"`). For the example above it would be `plugin:test`.
To render a view you can either write the namespace yourself or use the helper method `view()` in the plugin class.

### Routes
In the boot() method of your plugin call `$this->enableRoutes()`.
Optional you can pass a relative path to the routes file, default to `src/routes.php`.
You automatically have access to the `$app` variable.
Routes are automatically grouped to your plugin namespace, so you only have to type the controller name without the namespace.

### Controllers
Controllers must be in PluginDirectory->Http->Controllers.

### How to extend another plugin

## ToDo
- Move plugin directory and project namespace to publishable config file
