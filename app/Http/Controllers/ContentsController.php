<?php

namespace App\Http\Controllers;

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
     * @param  string  $contentId
     * @return \Illuminate\Http\Response
     */
    public function show($contentId)
    {
        $params = $this->content->getContentView($contentId);
        // Open Graph image 추출
        $params = array_add($params, 'ogImage', pullOutImage($params['content']->content));

        $skin = $params['content']->skin ? : 'default';

        return viewDefault("content.$skin.show", $params);
    }


}
