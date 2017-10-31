<?php

namespace Modules\Content\Policies;

use App\Models\User;
use Modules\Content\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the content.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Content\Models\Content  $content
     * @return mixed
     */
    public function index(User $user, Content $content)
    {
        $menuCode = ['300400', 'r'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create contents.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['300400', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the content.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Content\Models\Content  $content
     * @return mixed
     */
    public function update(User $user, Content $content)
    {
        $menuCode = ['300400', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the content.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Content\Models\Content  $content
     * @return mixed
     */
    public function delete(User $user, Content $content)
    {
        $menuCode = ['300400', 'd'];
        return getManageAuthModel($menuCode);
    }
}
