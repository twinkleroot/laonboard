<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Content;

class ContentsController extends Controller
{
    public $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = $this->content->getContentView($id);
        $skin = $params['content']->skin ? : 'default';

        return view()->exists("content.$skin.show") ? view("content.$skin.show", $params) : view("content.default.show", $params);
    }


}
