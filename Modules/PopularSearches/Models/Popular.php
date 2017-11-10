<?php

namespace Modules\PopularSearches\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;
use Carbon\Carbon;
use App\Models\Config;

class Popular extends Model
{
    public $timestamps = false;
    public $table = 'populars';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 인기 검색어 목록
    public function getIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $pageRows = cache('config.homepage')->pageRows;

        $query = Popular::select('*');
        switch ($kind) {
            case 'word':
                $query = $query->whereRaw("INSTR($kind, '$keyword')");
                break;
            case 'date':
                $query = $query->where('date', $keyword);
                break;
            default:
                break;
        }

        // 정렬
        if($order) {
            $query = $query->orderBy($order, $direction);
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        $populars = $query->paginate($pageRows);

        return [
            'populars' => $populars,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
        ];
    }

    // 인기 검색어 선택 삭제
    public function deletePopularWords($request)
    {
        $idArr = explode(',', $request->ids);
        return Popular::destroy($idArr);
    }

    // 인기 검색어 순위
    public function getPopularRank($request)
    {
        $fromDate = isset($request->fromDate) ? $request->fromDate : Carbon::now()->toDateString();
        $toDate = isset($request->toDate) ? $request->toDate : Carbon::now()->toDateString();
        $listType = isset($request->list) ? $request->list : 0; // 전체목록인지 기간검색인지
        $pageRows = cache('config.homepage')->pageRows;
        $query =
            Popular::select('word', DB::raw('count(*) as cnt'))
            ->whereRaw("trim(word) <> ''");
        if( !$listType ) {
            $query = $query->whereBetween('date', [$fromDate, $toDate]);
        } else {
            $fromDate = '';
            $toDate = '';
        }
        $ranks = $query->groupBy('word')
            ->orderByRaw('cnt desc, word asc')
            ->paginate($pageRows);

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'ranks' => $ranks,
        ];
    }

    public function updatePopular($request)
    {
        Cache::forget("config.popular");

        $data = array_except($request->all(), ['_method', '_token']);
        $message = '';

        $config = new Config();
        if($config->updateConfigByOne('popular', $data)) {
            $message = '인기 검색어 설정을 변경하였습니다.';
        }

        return $message;
    }
}
