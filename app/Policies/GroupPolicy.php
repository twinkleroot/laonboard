<?php

namespace App\Policies;

use App\User;
use App\Admin\Group;
use App\Common\Util;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the group.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\Group  $group
     * @return mixed
     */
    public function index(User $user, Group $group)
    {
        $menuCode = ['300200', 'r'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        $menuCode = ['300200', 'w'];
        return Util::getManageAuthModel($menuCode) && ($user->email == $group->admin);
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        $menuCode = ['300200', 'd'];
        return Util::getManageAuthModel($menuCode) && ($user->email == $group->admin);
    }
}
