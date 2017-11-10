<?php

namespace Modules\PopularSearches\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\PopularSearches\Models\Popular;

class PopularSearchesController extends Controller
{
    public $popular;

    public function __construct(Popular $popular)
    {
        $this->popular = $popular;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->popular)) {
            abort(403, '인기 검색어 관리 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->popular->getIndexParams($request);

        return view("modules.popularsearches.admin.index", $params);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        if (auth()->user()->cant('update', $this->popular)) {
            abort(403, '인기 검색어 설정 변경에 대한 권한이 없습니다.');
        }

        $message = $this->popular->updatePopular($request);

        return redirect()->back()->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->popular)) {
            abort(403, '인기 검색어 삭제에 대한 권한이 없습니다.');
        }

        $this->popular->deletePopularWords($request);

        return redirect()->back();
    }

    /**
     * Display a listing of popular searches rank
     * @return Response
     */
    public function rank(Request $request)
    {
        if (auth()->user()->cant('rank', $this->popular)) {
            abort(403, '인기 검색어 순위 보기에 대한 권한이 없습니다.');
        }

        $params = $this->popular->getPopularRank($request);

        return view("modules.popularsearches.admin.rank", $params);
    }
}
