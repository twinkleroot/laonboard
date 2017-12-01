<?php

namespace Modules\PopularSearches\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\PopularSearches\Models\Popular;
use Carbon\Carbon;

class AddPopularKeywordListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $request = request();
        $kind = $request->filled('kind') ? $request->kind : 'subject||content';     // 검색필드
        $keyword = $request->filled('keyword') ? $request->keyword : '';            // 검색어
        $keywords = explode(' ', strip_tags($keyword));                             // 검색어를 구분자로 나눈다.

        // 이메일, 글쓴이 검색이 아닐 때만 인기 검색어 추가
        if(str_contains($kind, 'subject') || str_contains($kind, 'content')) {
            foreach($keywords as $word) {
                $this->addPopular($word);
            }
        }
    }

    // 인기 검색어 추가
    private function addPopular($word)
    {
        $property = [
            'word' => $word,
            'date' => Carbon::now()->toDateString(),
            'ip' => request()->ip(),
        ];

        if(!Popular::where($property)->first()) {
            Popular::insert($property);
        }
    }
}
