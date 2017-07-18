<?php

namespace App\Policies;

use App\User;
use App\Admin\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\AdminUser\  $user
     * @return mixed
     */
    public function index(User $user, AdminUser $adminUser)
    {
        $menuCode = ['200100', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['200100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\AdminUser\  $user
     * @return mixed
     */
    public function update(User $user, AdminUser $adminUser)
    {
        $menuCode = ['200100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\AdminUser\  $user
     * @return mixed
     */
    public function delete(User $user, AdminUser $adminUser)
    {
        $menuCode = ['200100', 'd'];
        return getManageAuthModel($menuCode);
    }
}
