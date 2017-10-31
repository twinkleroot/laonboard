<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Contracts\BoardInterface;
use App\Models\User;

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
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return mixed
     */
    public function index(User $user, BoardInterface $board)
    {
        $menuCode = ['300100', 'r'];
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
        $menuCode = ['300100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can update the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return mixed
     */
    public function update(User $user, BoardInterface $board)
    {
        $menuCode = ['300100', 'w'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return mixed
     */
    public function delete(User $user, BoardInterface $board)
    {
        $menuCode = ['300100', 'd'];
        return getManageAuthModel($menuCode);
    }

    /**
     * Determine whether the user can delete the board.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return mixed
     */
    public function copy(User $user)
    {
        $menuCode = ['300100', 'w'];
        return getManageAuthModel($menuCode);
    }
}
