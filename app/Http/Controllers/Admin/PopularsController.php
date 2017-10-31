<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Popular;

class PopularsController extends Controller
{
    public $popular;

    public function __construct(Popular $popular)
    {
        $this->popular = $popular;
    }

    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->popular)) {
            abort(403, '인기 검색어 관리 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->popular->getIndexParams($request);

        return view("admin.populars.index", $params);
    }

    public function destroy($ids)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->popular)) {
            abort(403, '인기 검색어 삭제에 대한 권한이 없습니다.');
        }

        $this->popular->deletePopularWords($ids);

        return redirect()->back();
    }

    public function rank(Request $request)
    {
        if (auth()->user()->cant('rank', $this->popular)) {
            abort(403, '인기 검색어 순위 보기에 대한 권한이 없습니다.');
        }

        $params = $this->popular->getPopularRank($request);

        return view("admin.populars.rank", $params);
    }
}
