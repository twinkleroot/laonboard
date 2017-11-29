<?php

namespace Modules\Content\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Content\Models\Content;
use Schema;

class EventListener
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
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        // 레이아웃 하단에 내용관리 목록을 추가
        $events->listen(
            \Modules\Content\Events\AddFooterContent::class,
            __CLASS__. '@addFooterContent'
        );
        // 메뉴설정의 대상선택에 select option 추가
        $events->listen(
            \Modules\Content\Events\AddMenuSelectOption::class,
            __CLASS__. '@addMenuSelectOption'
        );
        // 메뉴설정 대상선택에 '내용관리'를 선택할 때 나오는 리스트 추가
        $events->listen(
            \Modules\Content\Events\AddMenuResult::class,
            __CLASS__. '@addMenuResult'
        );
    }

    /**
     * 레이아웃 하단(footerContent)에 내용관리 보기 목록 추가
     *
     * @param \Modules\Content\Events\AddFooterContent $event
     */
    public function addFooterContent(\Modules\Content\Events\AddFooterContent $event)
    {
        if(Schema::hasTable('contents')) {
            foreach(Content::where('show', 1)->orderBy('content_id')->get() as $content) {
                echo "<a href=\"". route('content.show', $content->content_id) ."\">". $content->subject. "</a>";
            }
        }
    }

    /**
     * 메뉴설정의 대상선택에 select option 추가
     *
     * @param \Modules\Content\Events\AddMenuSelectOption $event
     */
    public function addMenuSelectOption(\Modules\Content\Events\AddMenuSelectOption $event)
    {
        echo "<option value=\"content\">내용관리</option>";
    }

    /**
     * 메뉴설정 대상선택에 '내용관리'를 선택할 때 나오는 리스트 추가
     *
     * @param \Modules\Content\Events\AddMenuResult $event
     */
    public function addMenuResult(\Modules\Content\Events\AddMenuResult $event)
    {
        if($event->type == "content") {
            $contents = Content::orderBy('content_id')->get();
            $result = '';
            foreach ($contents as $content) {
                $content = cookingMenuSubject($content, $content->content_id);
                $result .=
                    "<tr>
                        <td>" . $content->subject. "</td>
                        <td class=\"td_mngsmall text-center\">
                            <input type=\"hidden\" name=\"subject[]\" value=\"". preg_replace('/[\'\"]/', '', $content->subject). "\">
                            <input type=\"hidden\" name=\"link[]\" value=\"". route('content.show', $content->content_id). "\">
                            <button type=\"button\" class=\"btn btn-default add_select\">선택</button>
                        </td>
                    </tr>";
            }

            echo $result;
        }
    }

}
