<?php

namespace App\Policies;

use App\User;
use App\Admin\GroupUser;
use App\Common\Util;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupUserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the groupUser.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\GroupUser  $groupUser
     * @return mixed
     */
    public function index(User $user, GroupUser $groupUser)
    {
        $menuCode = ['300200', 'r'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create groupUsers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['300200', 'w'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the groupUser.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\GroupUser  $groupUser
     * @return mixed
     */
    public function delete(User $user, GroupUser $groupUser)
    {
        $menuCode = ['300200', 'd'];
        return Util::getManageAuthModel($menuCode);
    }
}
