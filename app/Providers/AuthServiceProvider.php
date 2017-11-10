<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Point;
use App\Models\AdminUser;
use App\Models\Popular;
use App\Models\GroupUser;
use App\Models\Board;
use App\Models\Group;
use App\Models\ModuleSource;
use App\Policies\BoardPolicy;
use App\Policies\UserPolicy;
use App\Policies\PointPolicy;
use App\Policies\GroupPolicy;
use App\Policies\GroupUserPolicy;
use App\Policies\PopularPolicy;
use App\Policies\ModulePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
        Group::class => GroupPolicy::class,
        GroupUser::class => GroupUserPolicy::class,
        Point::class => PointPolicy::class,
        Popular::class => PopularPolicy::class,
        AdminUser::class => UserPolicy::class,
        ModuleSource::class => ModulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // 메일 테스트
        Gate::define('view-admin-mailtest', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });

        // phpinfo()
        Gate::define('view-admin-phpinfo', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });

        // 부가서비스
        Gate::define('view-admin-extra_service', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });

        // 글, 댓글 현황
        Gate::define('view-admin-status', function($user, $manageAuth) {
            if($manageAuth) {
                return true;
            }
            return false;
        });
    }
}
