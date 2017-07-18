<?php

namespace App\Policies;

use App\User;
use App\Admin\Point;
use Illuminate\Auth\Access\HandlesAuthorization;

class PointPolicy
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
     * @param  \App\User  $user
     * @param  \App\Admin\Point  $point
     * @return mixed
     */
    public function index(User $user, Point $point)
    {
        $menuCode = ['200200', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create points.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['200200', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the point.
     *
     * @param  \App\User  $user
     * @param  \App\Admin\Point  $point
     * @return mixed
     */
    public function delete(User $user, Point $point)
    {
        $menuCode = ['200200', 'd'];
        return getManageAuthModel($menuCode);
    }
}
