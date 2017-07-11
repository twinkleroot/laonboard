<?php

namespace App\Policies;

use App\User;
use App\Admin\Popular;
use App\Common\Util;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopularPolicy
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
    public function index(User $user, Popular $popular)
    {
        $menuCode = ['300300', 'r'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\AdminUser\  $user
     * @return mixed
     */
    public function rank(User $user, Popular $popular)
    {
        $menuCode = ['300310', 'r'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\AdminUser\  $user
     * @return mixed
     */
    public function delete(User $user, Popular $popular)
    {
        $menuCode = ['300300', 'd'];
        return Util::getManageAuthModel($menuCode);
    }
}
