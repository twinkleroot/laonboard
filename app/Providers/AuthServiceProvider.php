<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Admin\Board;
use App\Admin\Group;
use App\Admin\Point;
use App\Admin\AdminUser;
use App\Admin\Content;
use App\Admin\GroupUser;
use App\Policies\BoardPolicy;
use App\Policies\UserPolicy;
use App\Policies\GroupPolicy;
use App\Policies\PointPolicy;
use App\Policies\GroupUserPolicy;
use App\Policies\ContentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
        AdminUser::class => UserPolicy::class,
        Group::class => GroupPolicy::class,
        GroupUser::class => GroupUserPolicy::class,
        Content::class => ContentPolicy::class,
        Point::class => PointPolicy::class,
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
