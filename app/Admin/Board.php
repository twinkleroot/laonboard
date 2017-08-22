<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Admin\Group;
use App\Write;
use DB;
use Cache;
use File;
use Carbon\Carbon;
use App\Services\BoardSingleton;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Board extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct()
    {
        $this->table = 'boards';
    }


    // 게시판 그룹 모델과의 관계 설정
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public static function getBoard($boardId, $key='id')
    {
        return BoardSingleton::getInstance($boardId, $key);
    }

    // (게시판 관리) index 페이지에서 필요한 파라미터 가져오기
    public function getBoardIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $query = '';
        // 최고 관리자가 아닌 관리자의 경우
        if( !auth()->user()->isSuperAdmin() ) {
            $query =
                Board::select('boards.*')
                ->leftJoin('groups', 'boards.group_id', '=', 'groups.id')
                ->where('groups.admin', auth()->user()->email);
            if($kind) {
                if($kind == 'group_id') {
                    $query = $query->where('groups.group_id', $keyword);
                } else {
                    $query = $query->where($kind, 'like', '%'. $keyword. '%');
                }
            }
        } else {
            if($kind) {
                if($kind == 'group_id') {
                    $query =
                        Board::select('boards.*')
                        ->leftJoin('groups', 'boards.group_id', '=', 'groups.id')
                        ->where('groups.group_id', 'like', '%'. $keyword. '%');
                } else {
                    $query = Board::where($kind, 'like', '%'. $keyword. '%');
                }
            } else {
                $query = Board::select('*');
            }
        }

        // 정렬
        if($order) {
            $query = $query->orderBy('boards.'. $order, $direction);
        } else {
            $query = $query->orderBy('order', 'group_id', 'table_name');
        }

        $boards = $query->paginate(cache("config.homepage")->pageRows);
        $groups = Group::get();

        $queryString = "?kind=$kind&keyword=$keyword&page=". $boards->currentPage();

        return [
            'boards' => $boards,
            'groups' => $groups,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
            'queryString' => $queryString,
            'skins' => getSkins('board'),
            // 'mobileSkins' => count(getSkins('boardMobile')) == 1 ? getSkins('board') : getSkins('boardMobile'),
        ];
    }

    // (게시판 관리) create 페이지에서 필요한 파라미터 가져오기
    public function getBoardCreateParams($request)
    {
        $groups = Group::get();
        $inputGroupId = $request->input('group_id');
        $selectedGroup = '';

        if(!is_null($inputGroupId)) {
            $selectedGroup = Group::findOrFail($inputGroupId)->id;
        }

        $config = cache("config.board");

        $board = [
            'read_point' => $config->readPoint,
            'write_point' => $config->writePoint,
            'comment_point' => $config->commentPoint,
            'download_point' => $config->downloadPoint,
            'use_secret' => 0,
            'count_modify' => 1,
            'count_delete' => 1,
            'page_rows' => cache("config.homepage")->pageRows,
            // 'mobile_page_rows' => cache("config.homepage")->mobilePageRows,
            'skin' => 'default',
            // 'mobile_skin' => 'default',
            'layout' => 'default.basic',
            'gallery_cols' => 4,
            'gallery_width' => 174,
            'gallery_height' => 124,
            // 'mobile_gallery_width' => 125,
            // 'mobile_gallery_height' => 100,
            'table_width' => 100,
            'subject_len' => 60,
            // 'mobile_subject_len' => 30,
            'new' => 24,
            'hot' => 100,
            'image_width' => 600,
            'upload_count' => 2,
            'upload_size' => 1048576,
            'reply_order' => 1,
            'use_search' => 1,
            'content_head' => '',
            'content_tail' => '',
            'insert_content' => '',
        ];

        return [
            'homePageConfig' => cache("config.homepage"),
            'boardConfig' => $config,
            'board' => $board,      // 배열
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'action' => route('admin.boards.store'),
            'type' => 'create',
            'queryString' => '',
            'skins' => getSkins('board'),
            // 'mobileSkins' => count(getSkins('boardMobile')) > 1 ? getSkins('boardMobile') : getSkins('board'),
        ];
    }

    // (게시판 관리) board 테이블에 새 게시판 행 추가
    public function storeBoard($data)
    {
        $data = array_except($data, ['_token']);

        $data = exceptNullData($data);

        // 그룹 적용, 전체 적용 수행(그리고 사용한 필드를 배열에서 제외시킴.)
        $data = $this->applyBoard($data, 'chk_group');
        $data = $this->applyBoard($data, 'chk_all');

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        // board 테이블에 새 게시판 행 추가
        $boardId = Board::insertGetId($data);
        if($boardId) {
            return Board::getBoard($boardId);
        }

        abort(500, '게시판 생성에 실패하였습니다.');
    }

    // (게시판 관리) edit 페이지에서 필요한 파라미터 가져오기
    public function getBoardEditParams($request, $id)
    {
        $board = Board::getBoard($id);
        $groups = Group::get();
        $kind = $request->has('kind') ? $request->kind : '';
        $keyword = $request->has('keyword') ? $request->keyword : '';
        $order = $request->has('order') ? $request->order : '';
        $direction = $request->has('direction') ? $request->direction : '';
        $page = $request->has('page') ? $request->page : '';

        $queryString = "?kind=$kind&keyword=$keyword&order=$order&direction=$direction&page=$page";

        return [
            'boardConfig' => cache("config.board"),
            'board' => $board,      // 객체
            'groups' => $groups,
            'action' => route('admin.boards.update', $id),
            'type' => 'edit',
            'skins' => getSkins('board'),
            'queryString' => $queryString,
            // 'mobileSkins' => count(getSkins('boardMobile')) > 1 ? getSkins('boardMobile') : getSkins('board'),
        ];
    }

    // (게시판 관리) 정보 수정
    public function updateBoard($data, $id)
    {
        $board = Board::getBoard($id);

        if(isset($data['procCount'])) {
            $write = new Write();
            $write->setTableName($board->table_name);
            // 원글 수
            $countWrite = $write->where('is_comment', 0)->count();
            $countComment = $write->where('is_comment', 1)->count();

            $board->count_write = $countWrite;
            $board->count_comment = $countComment;

            $board->save();
        }

        $data = array_except($data, ['_token', '_method', 'id', 'queryString', 'procCount']);
        foreach($board->attributes as $key => $value) {
            // 체크박스 체크가 되었었다가 안된 필드는 0으로 업데이트 해야한다.
            if($key == 'id') {
                continue;
            }
            if($value == 1 && !isset($data[$key])) {
                $data = array_add($data, $key, 0);
            } else if(in_array($key, ['write_min', 'write_max', 'comment_min', 'comment_max', 'order']) && !isset($data[$key])) {
                // 기본값 적용 필드
                $data = array_add($data, $key, 0);
            }
        }

        // 그룹 적용, 전체 적용 수행(그리고 사용한 필드를 배열에서 제외시킴.)
        $data = $this->applyBoard($data, 'chk_group');
        $data = $this->applyBoard($data, 'chk_all');

        // 해당 게시판의 수정을 수행한다.
        if($board->update($data)) {
            return $board->subject;
        } else {
            return false;
        }
    }

    // (게시판 관리) 그룹 적용, 전체 적용
    public function applyBoard($data, $prefix)
    {
        $start = strlen($prefix) + 1;
        $extraStart = strlen($prefix . '_extra') + 1;

        $updateData = [];

        foreach($data as $key => $value) {
            if(str_contains($key, $prefix)) {       // 입력된 data 중에서 name 값이 $prefix을 포함하는 필드가 있다면
                if(isset($data[substr($key, $start)])) {    // 그룹적용에 해당하는 필드에 넣을 값이 존재하는지 확인.
                    $updateKey = substr($key, $start);
                    $updateValue = $data[$updateKey];
                    $updateData = array_add($updateData, $updateKey, $updateValue);
                }
                $data = array_except($data, [$key]);    // $prefix로 시작하는 필드는 제외.
            }

            // 여분 필드 처리
            if(str_contains($key, $prefix . '_extra')) {       // 입력된 data 중에서 name 값이 $prefix + extra를 포함하는 필드가 있다면
                // 여분 필드 제목
                $subjKey = 'subj_' . substr($key, $extraStart);
                $subjValue = $data[$subjKey];
                if(!is_null($subjValue)) {
                    $updateData = array_add($updateData, $subjKey, $subjValue);
                }

                // 여분 필드 값
                $valueKey = 'value_' . substr($key, $extraStart);
                $valueValue = $data[$valueKey];
                if(!is_null($valueValue)) {
                    $updateData = array_add($updateData, $valueKey, $valueValue);
                }
                $data = array_except($data, [$key]);    // $prefix로 시작하는 필드는 제외.
            }
        }

        // 새로 생성하는 게시판이 속한 그룹의 게시판의 해당 필드값을 변경한다.
        if(str_contains($prefix, 'group')) {
            Board::where('group_id', $data['group_id'])->update($updateData);
        } else { // 모든 게시판의 해당 필드값을 변경한다.
            Board::where('id', '>', '0')->update($updateData);
        }

        return $data;
    }

    // (게시판 관리) 게시판 구조 복사
    public function copyBoard($data)
    {
        $data = array_except($data, ['_token']);

        $originalData = Board::getBoard($data['id'])->toArray();

        $originalData['table_name'] = $data['table_name'];
        $originalData['subject'] = $data['subject'];
        // 구조만 복사시에는 공지사항 번호는 복사하지 않는다.
        if($data['copy_case'] == 'schema_only') {
            $originalData['notice'] = '';
        }
        $originalData = exceptNullData($originalData);
        $originalData = array_except($originalData, ['id']);

        $newBoardId = Board::insertGetId($originalData);

        return Board::find($newBoardId);
    }

    // (게시판 관리) 선택 삭제
    public function deleteBoards($ids)
    {
        $idArr = explode(',', $ids);
        foreach($idArr as $id) {
            Schema::dropIfExists('write_'. Board::getBoard($id)->table_name);
        }
        $result = Board::whereRaw('id in (' . $ids . ') ')->delete();

        if($result > 0) {
            return '선택한 게시판이 삭제되었습니다.';
        } else {
            return '선택한 게시판의 삭제가 실패하였습니다.';
        }
    }

    // (게시판 관리) 선택 수정
    public function selectedUpdate($request)
    {
        $idArr = explode(',', $request->get('ids'));
        $groupIdArr = explode(',', $request->get('group_ids'));
        $skinArr = explode(',', $request->get('skin_ids'));
        // $mobileSkinArr = explode(',', $request->get('mobile_skin_ids'));
        $subjectArr = explode(',', $request->get('subjects'));
        $readPointArr = explode(',', $request->get('read_points'));
        $writePointArr = explode(',', $request->get('write_points'));
        $commentPointArr = explode(',', $request->get('comment_points'));
        $downloadPointArr = explode(',', $request->get('download_points'));
        $useSnsArr = explode(',', $request->get('use_snss'));
        $useSearchArr = explode(',', $request->get('use_searchs'));
        $orderArr = explode(',', $request->get('orders'));
        // $deviceArr = explode(',', $request->get('devices'));

        $index = 0;
        foreach($idArr as $id) {
            $board = Board::getBoard($id);

            if(!is_null($board)) {
                $board->update([
                    'group_id' => $groupIdArr[$index],
                    'skin' => $skinArr[$index],
                    // 'mobile_skin' => $mobileSkinArr[$index],
                    'subject' => $subjectArr[$index],
                    'read_point' => $readPointArr[$index],
                    'write_point' => $writePointArr[$index],
                    'comment_point' => $commentPointArr[$index],
                    'download_point' => $downloadPointArr[$index],
                    'use_sns' => $useSnsArr[$index],
                    'use_search' => $useSearchArr[$index],
                    'order' => $orderArr[$index],
                    // 'device' => $deviceArr[$index],
                ]);
                $index++;
            } else {
                abort('500', '정보를 수정할 게시판이 존재하지 않습니다. 게시판이 잘 선택 되었는지 확인해 주세요.');
            }
        }
    }

    // (게시판 관리 -> 게시판 추가) 새 게시판 테이블 생성
    public function createWriteTable($tableName)
    {
        $tableNameAddPrefix = "write_$tableName";
        if(!Schema::hasTable($tableNameAddPrefix)) {
            Schema::create($tableNameAddPrefix, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('num')->default(0);
                $table->string('reply', 10)->default('');
                $table->integer('parent')->unsigned()->default(0);
                $table->tinyInteger('is_comment')->default(0);

                $table->index(['num', 'reply', 'parent'], 'num_reply_parent');
                $table->index(['is_comment', 'id'], 'is_comment');

                $table->integer('comment')->unsigned()->default(0);
                $table->string('comment_reply', 5)->nullable();
                $table->string('ca_name')->nullable();
                $table->enum('option', ['html1', 'html2', 'secret', 'mail'])->nullable();
                $table->string('subject')->nullable();
                $table->text('content')->nullable();
                $table->text('link1')->nullable();
                $table->text('link2')->nullable();
                $table->integer('link1_hit')->unsigned()->default(0);
                $table->integer('link2_hit')->unsigned()->default(0);
                $table->integer('hit')->unsigned()->default(0);
                $table->integer('good')->unsigned()->default(0);
                $table->integer('nogood')->unsigned()->default(0);
                $table->integer('user_id')->unsigned();
                $table->string('password')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('homepage')->nullable();
                $table->timestamps();
                $table->tinyInteger('file')->default(0);
                $table->string('last', 19)->nullable();
                $table->string('ip')->nullable();
                $table->string('facebook_user')->nullable();
                $table->string('twitter_user')->nullable();
                $table->string('extra_1')->nullable();
                $table->string('extra_2')->nullable();
                $table->string('extra_3')->nullable();
                $table->string('extra_4')->nullable();
                $table->string('extra_5')->nullable();
                $table->string('extra_6')->nullable();
                $table->string('extra_7')->nullable();
                $table->string('extra_8')->nullable();
                $table->string('extra_9')->nullable();
                $table->string('extra_10')->nullable();
            });

            // 라라벨 기본 API에서 mysql의 set type을 지원하지 않으므로 enum으로 생성하고 set으로 변경한다.
            // $table_prefix = DB::getTablePrefix();
            DB::statement("ALTER TABLE " . DB::getTablePrefix(). $tableNameAddPrefix . " CHANGE `option` `option` SET('html1', 'html2', 'secret', 'mail');");

            return true;
        } else {
            return false;
        }
    }

    // 게시판 썸네일 삭제
    public function deleteThumbnail($dirName, $id)
    {
        $path = storage_path("app/public/$dirName");
        $files = File::files($path);
        $results = [];
        foreach($files as $file) {
            $baseFileName = basename($file);
            if(substr($baseFileName, 0, 6) == 'thumb-') {
                $results[] = $file;
            }
        }

        return [
            'board' => Board::getBoard($id),
            'files' => $results
        ];
    }

}
