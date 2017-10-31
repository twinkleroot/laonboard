<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AdminUser;
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
     * @param  \App\Models\User  $user
     * @param  \App\Models\AdminUser\  $user
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
     * @param  \App\Models\User  $user
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
     * @param  \App\Models\User  $user
     * @param  \App\Models\AdminUser\  $user
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
     * @param  \App\Models\User  $user
     * @param  \App\Models\AdminUser\  $user
     * @return mixed
     */
    public function delete(User $user, AdminUser $adminUser)
    {
        $menuCode = ['200100', 'd'];
        return getManageAuthModel($menuCode);
    }
}
