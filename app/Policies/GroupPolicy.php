<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Group;
use App\Models\User;

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
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function index(User $user, Group $group)
    {
        $menuCode = ['300200', 'r'];
        return getManageAuthModel($menuCode);
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
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        $menuCode = ['300200', 'w'];
        return getManageAuthModel($menuCode) && ($user->email == $group->admin);
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        $menuCode = ['300200', 'd'];
        return getManageAuthModel($menuCode) && ($user->email == $group->admin);
    }
}
