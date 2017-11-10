<?php

namespace Modules\CustomMain\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // index
        Gate::define('module-custommain-index', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });

        // update
        Gate::define('module-custommain-update', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });

    }
}
