<?php

namespace Modules\PopularSearches\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PopularSearches\Models\Popular;
use App\Models\User;

class PopularSearchesPolicy
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
     * @param  \Modules\PopularSearches\Models\Popular  $popular
     * @return mixed
     */
    public function index(User $user, Popular $popular)
    {
        $menuCode = ['400100', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\PopularSearches\Models\Popular  $popular
     * @return mixed
     */
    public function update(User $user, Popular $popular)
    {
        $menuCode = ['400100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\PopularSearches\Models\Popular  $popular
     * @return mixed
     */
    public function rank(User $user, Popular $popular)
    {
        $menuCode = ['400100', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\PopularSearches\Models\Popular  $popular
     * @return mixed
     */
    public function delete(User $user, Popular $popular)
    {
        $menuCode = ['400100', 'd'];
        return getManageAuthModel($menuCode);
    }
}
