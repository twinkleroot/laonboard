<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Group;
use App\Admin\Board;
use Cache;
use DB;

class Menu extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'menus';
    }

    // 메뉴 리스트 페이지에서 필요한 파라미터
    public function getMenuIndexParams()
    {
        return [
            'menus' => Menu::all(),
            'maxCode' => Menu::max('code'),
            'config' => Cache::get("config.homepage"),
        ];
    }

    // 메뉴 추가 페이지에서 필요한 파라미터
    public function getMenuCreateParams($request)
    {
        $code = $request->get('code');
        $new = $request->get('new');

        if($new == 'new' || !$code) {
            $code = base_convert(mb_substr($code, 0, 2, 'utf-8'), 36, 10);
            $code += 36;
            $code = base_convert($code, 10, 36);
        }

        return [
            'config' => Cache::get("config.homepage"),
            'code' => $code,
            'new' => $new,
        ];
    }

    // 메뉴 추가 팝업창에 대상 선택에 따라서 view를 load할 때 필요한 파라미터
    public function menuResult($type)
    {
        $results = null;
        switch ($type) {
            case 'group':
                $results = Group::select('id', 'group_id', 'subject')->orderBy('order', 'desc')->orderBy('group_id', 'desc')->get();
                foreach($results as $result) {
                    $result = $this->cookingSubject($result, $result->id);
                }
                break;
            case 'board':
                $results = Board::select('id', 'subject')->orderBy('order', 'desc')->orderBy('id', 'desc')->get();
                foreach($results as $result) {
                    $result = $this->cookingSubject($result, $result->id);
                }
                break;
            case 'content':
                $results = Content::orderBy('id', 'desc')->get();
                foreach($results as $result) {
                    $result = $this->cookingSubject($result, $result->content_id);
                }
                break;
            default:
                # code...
                break;
        }

        return [
            'type' => $type,
            'results' => $results,
        ];
    }

    private function cookingSubject($result, $id)
    {
        $menu = Menu::where('name', $result->subject)->whereRaw("INSTR(link, '$id')")->first();
        if($menu) {
            $result->subject .= ' (이미 추가 된 메뉴)';
        }
        return $result;
    }

    // Menu 테이블의 모든 데이터를 삭제하고 auto-incrementing ID를 0으로 초기화 한다.
    public function initMenu()
    {
        Menu::truncate();

        Cache::forget('menuList');
        Cache::forget('subMenuList');
    }

    // 입력된 폼을 분석해서 code를 생성하고 메뉴 정보를 저장
    public function saveMenu($menus)
    {
        $menus = array_except($menus, ['_token']);

        $groupCode = null;
        $primaryCode = null;

        $count = count($menus['code']);

        for($i=0; $i<$count; $i++) {
            $code = $menus['code'][$i];
            $subCode = '';
            if($groupCode == $code) {
                $row = Menu::selectRaw('max(substring(code,3,2)) as max_code')
                    ->whereRaw('SUBSTRING(code,1,2) ='. $primaryCode)->first();

                $subCode = base_convert($row['max_code'], 36, 10);
                $subCode += 36;
                $subCode = base_convert($subCode, 10, 36);

                $finalCode = $primaryCode . $subCode;
            } else {
                $row = Menu::selectRaw('max(substring(code,1,2)) as max_code')
                    ->whereRaw('length(code) = 2')->first();

                $finalCode = base_convert($row['max_code'], 36, 10);
                $finalCode += 36;
                $finalCode = base_convert($finalCode, 10, 36);

                $groupCode = $code;
                $primaryCode = $finalCode;
            }

            $menus['code'][$i] = $finalCode;

            Menu::insert([
                'code' => $menus['code'][$i],
                'name' => $menus['name'][$i],
                'link' => is_null($menus['link'][$i]) ? "#" : $menus['link'][$i],
                'target' => $menus['target'][$i],
                'order' => $menus['order'][$i],
                'use' => $menus['use'][$i],
                // 'mobile_use' => $menus['mobile_use'][$i],
            ]);
        }

        $this->registerCache();
    }

    // 메뉴 저장 후 캐시에 등록
    private function registerCache()
    {
        $menuList = Cache::rememberForever("menuList", function() {
            return $this->getMainMenu();
        });
        Cache::rememberForever("subMenuList", function() use($menuList){
            return $this->getSubMenuList($menuList);
        });
    }

    // 메뉴 테이블에 저장한 대메뉴 리스트 가져오기
    public function getMainMenu()
    {
        return Menu::where('use', 1)
                    ->whereRaw('length(code) = 2')
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
    }

    // 메뉴 테이블에 저장한 소메뉴 리스트 가져오기
    public function getSubMenuList($menuList)
    {
        $subMenuList = [];
        for($i=0; $i<count($menuList); $i++) {
            $subMenuList[$i] = Menu::where('use', 1)
                    ->whereRaw('length(code) = 4')
                    ->whereRaw('substring(code, 1, 2)=' . $menuList[$i]['code'])
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
        }

        return $subMenuList;
    }

}
