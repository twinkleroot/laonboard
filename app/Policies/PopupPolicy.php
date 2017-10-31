<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Popup;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the point.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Point  $point
     * @return mixed
     */
    public function index(User $user, Popup $popup)
    {
        $menuCode = ['100600', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create points.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['100600', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function update(User $user, Popup $popup)
    {
        $menuCode = ['100600', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the point.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Point  $point
     * @return mixed
     */
    public function delete(User $user, Popup $popup)
    {
        $menuCode = ['100600', 'd'];
        return getManageAuthModel($menuCode);
    }
}
