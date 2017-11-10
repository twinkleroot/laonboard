<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ModuleSource;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleSource  $module
     * @return mixed
     */
    public function index(User $user, ModuleSource $module)
    {
        $menuCode = ['400100', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create boards.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['400100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleSource  $module
     * @return mixed
     */
    public function update(User $user, ModuleSource $module)
    {
        $menuCode = ['400100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleSource  $module
     * @return mixed
     */
    public function delete(User $user, ModuleSource $module)
    {
        $menuCode = ['400100', 'd'];
        return getManageAuthModel($menuCode);
    }
}
