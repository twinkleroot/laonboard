<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupUser;
use App\User;
use App\Group;
use Carbon\Carbon;

class AccessibleGroupsController extends Controller
{

    public $groupUserModel;

    public function __construct(GroupUser $groupUser)
    {
        $this->middleware('level:10');

        $this->groupUserModel = $groupUser;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = $this->groupUserModel->showAccessibleGroups($id);

        return view('admin.group_user.show', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = $this->groupUserModel->addAccessibleGroups($request);

        return redirect(route('admin.accessGroups.show', $request->get('user_id')))
            ->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $message = $this->groupUserModel->delAccessibleGroups($request);
        return redirect(route('admin.accessGroups.show', $id))
            ->with('message', $message);
    }
}
