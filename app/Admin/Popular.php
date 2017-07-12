<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Carbon\Carbon;

class Popular extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'populars';

    // 인기 검색어 목록
    public function getIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $pageRows = Cache::get('config.homepage')->pageRows;

        $query = Popular::selectRaw('*');
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
    public function deletePopularWords($ids)
    {
        $idArr = explode(',', $ids);
        return Popular::destroy($idArr);
    }

    // 인기 검색어 추가(전체 검색에서 이용)
    public function addPopular($kinds, $keyword, $request)
    {
         if(!in_array('user_id', $kinds)) {
             Popular::firstOrCreate([
                 'word' => $keyword,
                 'date' => Carbon::now()->toDateString(),
                 'ip' => $request->ip(),
             ]);
         }
    }

    // 인기 검색어 순위
    public function getPopularRank($request)
    {
        $fromDate = isset($request->fromDate) ? $request->fromDate : Carbon::now()->toDateString();
        $toDate = isset($request->toDate) ? $request->toDate : Carbon::now()->toDateString();
        $listType = isset($request->list) ? $request->list : 0; // 전체목록인지 기간검색인지
        $pageRows = Cache::get('config.homepage')->pageRows;
        $query = Popular::selectRaw('word, count(*) as cnt')
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
}
