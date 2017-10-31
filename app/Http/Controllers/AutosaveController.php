<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autosave;

class AutosaveController extends Controller
{
    public $autosaveModel;

    public function __construct(Autosave $autosave)
    {
        $this->autosaveModel = $autosave;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = $this->autosaveModel->autosaveList();

        return $list;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->autosaveModel->autosave($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->autosaveModel->autosaveView($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->autosaveModel->autosaveDelete($id);
    }
}
