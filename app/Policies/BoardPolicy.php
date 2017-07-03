<?php

namespace App\Policies;

use App\User;
use App\Admin\Board;
use App\Common\Util;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoardPolicy
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
     * @param  \App\User  $user
     * @param  \App\Board  $board
     * @return mixed
     */
    public function index(User $user, Board $board)
    {
        $menuCode = ['300100', 'r'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can create boards.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $menuCode = ['300100', 'w'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  \App\User  $user
     * @param  \App\Board  $board
     * @return mixed
     */
    public function update(User $user, Board $board)
    {
        $menuCode = ['300100', 'w'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\User  $user
     * @param  \App\Board  $board
     * @return mixed
     */
    public function delete(User $user, Board $board)
    {
        $menuCode = ['300100', 'd'];
        return Util::getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\User  $user
     * @param  \App\Board  $board
     * @return mixed
     */
    public function copy(User $user)
    {
        $menuCode = ['300100', 'w'];
        return Util::getManageAuthModel($menuCode);
    }
}
