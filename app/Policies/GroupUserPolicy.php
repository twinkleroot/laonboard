<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GroupUser;
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
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupUser  $groupUser
     * @return mixed
     */
    public function index(User $user, GroupUser $groupUser)
    {
        $menuCode = ['300200', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create groupUsers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['300200', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the groupUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupUser  $groupUser
     * @return mixed
     */
    public function delete(User $user, GroupUser $groupUser)
    {
        $menuCode = ['300200', 'd'];
        return getManageAuthModel($menuCode);
    }
}
