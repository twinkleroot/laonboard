<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Point;

class PointsController extends Controller
{

    public $pointModel;

    public function __construct(Point $point)
    {
        $this->middleware('admin');

        $this->pointModel = $point;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = $this->pointModel->getPointIndexParams($request);

        return view('admin.points.index', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rule = [
            'email' => 'required|email',
            'point' => 'required|numeric',
            'content' => 'required',
        ];

        $this->validate($request, $rule);

        $message = $this->pointModel->givePoint($request->all());

        if($message != 'success') {
            return redirect(route('message'))->with('message', $message);
        } else {
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->pointModel->deletePointOnAdmin($id);

        return redirect()->back();
    }
}
