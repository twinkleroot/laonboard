<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Common\Util;
use App\Group;
use App\Write;
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

    // 게시판 그룹 모델과의 관계 설정
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // (게시판 관리) index 페이지에서 필요한 파라미터 가져오기
    public function getBoardIndexParams()
    {
        $config = Config::getConfig('config.homepage');
        $boards = Board::paginate($config->pageRows);;
        $groups = Group::get();

        return [
            'config' => $config,
            'boards' => $boards,
            'groups' => $groups,
            'kind' => '',
            'keyword' => '',
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

        $config = Config::getConfig('config.board');

        $board = [
            'read_point' => $config->readPoint,
            'write_point' => $config->writePoint,
            'comment_point' => $config->commentPoint,
            'download_point' => $config->downloadPoint,
            'use_secret' => 0,
            'count_modify' => 1,
            'count_delete' => 1,
            'page_rows' => config('gnu.pageRows'),             // 환경설정에 설정하는 폼 만들면 Config 모델에서 가져오도록 변경해야 함.
            'mobile_page_rows' => config('gnu.mobilePageRows'),      // 환경설정에 설정하는 폼 만들면 Config 모델에서 가져오도록 변경해야 함.
            'skin' => 'basic',
            'mobile_skin' => 'basic',
            'include_head' => '_head.php',
            'include_tail' => '_tail.php',
            'gallery_cols' => 4,
            'gallery_width' => 174,
            'gallery_height' => 124,
            'mobile_gallery_width' => 125,
            'mobile_gallery_height' => 100,
            'table_width' => 100,
            'subject_len' => 60,
            'mobile_subject_len' => 30,
            'new' => 24,
            'hot' => 100,
            'image_width' => 600,
            'upload_count' => 2,
            'upload_size' => 1048576,
            'reply_order' => 1,
            'use_search' => 1,
        ];

        return [
            'config' => Config::getConfig('config.homepage'),
            'board' => $board,      // 배열
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'title' => '생성',
            'action' => route('admin.boards.store'),
            'type' => 'create',
        ];
    }

    // (게시판 관리) edit 페이지에서 필요한 파라미터 가져오기
    public function getBoardEditParams($id)
    {
        $board = Board::findOrFail($id);
        $groups = Group::get();
        $keyword = Group::find($board->group_id)->group_id;

        return [
            'config' => Config::getConfig('config.homepage'),
            'board' => $board,      // 객체
            'groups' => $groups,
            'keyword' => $keyword,
            'title' => '수정',
            'action' => route('admin.boards.update', $id),
            'type' => 'edit',
        ];
    }

    // (게시판 관리) board 테이블에 새 게시판 행 추가
    public function createBoard($data)
    {
        $data = array_except($data, ['_token']);

        $data = Util::exceptNullData($data);

        // 그룹 적용, 전체 적용 수행(그리고 사용한 필드를 배열에서 제외시킴.)
        $data = $this->applyBoard($data, 'chk_group');
        $data = $this->applyBoard($data, 'chk_all');

        // board 테이블에 새 게시판 행 추가
        return Board::create($data);
    }

    // (게시판 관리) 정보 수정
    public function updateBoard($data, $id)
    {
        $data = array_except($data, ['_token']);
        $data = Util::exceptNullData($data);

        $board = Board::findOrFail($id);

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

        $originalData = Board::findOrFail($data['id'])->toArray();

        $originalData['table_name'] = $data['table_name'];
        $originalData['subject'] = $data['subject'];
        // 구조만 복사시에는 공지사항 번호는 복사하지 않는다.
        if($data['copy_case'] == 'schema_only') {
            $originalData['notice'] = '';
        }
        $originalData = Util::exceptNullData($originalData);
        $originalData = array_except($originalData, ['id']);

        return Board::create($originalData);
    }

    // (게시판 관리) 선택 삭제
    public function deleteBoards($ids)
    {
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
        $subjectArr = explode(',', $request->get('subjects'));
        $readPointArr = explode(',', $request->get('read_points'));
        $writePointArr = explode(',', $request->get('write_points'));
        $commentPointArr = explode(',', $request->get('comment_points'));
        $downloadPointArr = explode(',', $request->get('download_points'));
        $useSnsArr = explode(',', $request->get('use_snss'));
        $useSearchArr = explode(',', $request->get('use_searchs'));
        $orderArr = explode(',', $request->get('orders'));
        $deviceArr = explode(',', $request->get('devices'));

        $index = 0;
        foreach($idArr as $id) {
            $board = Board::find($id);

            if(!is_null($board)) {
                $board->update([
                    'group_id' => $groupIdArr[$index],
                    'subject' => $subjectArr[$index],
                    'read_point' => $readPointArr[$index],
                    'write_point' => $writePointArr[$index],
                    'comment_point' => $commentPointArr[$index],
                    'download_point' => $downloadPointArr[$index],
                    'use_sns' => $useSnsArr[$index],
                    'use_search' => $useSearchArr[$index],
                    'order' => $orderArr[$index],
                    'device' => $deviceArr[$index],
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
        $tableNameAddPrefix = 'write_' . $tableName;
        if(!Schema::hasTable($tableNameAddPrefix)) {
            Schema::create($tableNameAddPrefix, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('num')->default(0);
                $table->string('reply', 10)->nullable();
                $table->integer('parent')->unsigned()->default(0);
                $table->tinyInteger('is_comment')->default(0);
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
                $table->string('subj_1')->nullable();
                $table->string('subj_2')->nullable();
                $table->string('subj_3')->nullable();
                $table->string('subj_4')->nullable();
                $table->string('subj_5')->nullable();
                $table->string('subj_6')->nullable();
                $table->string('subj_7')->nullable();
                $table->string('subj_8')->nullable();
                $table->string('subj_9')->nullable();
                $table->string('subj_10')->nullable();
                $table->string('value_1')->nullable();
                $table->string('value_2')->nullable();
                $table->string('value_3')->nullable();
                $table->string('value_4')->nullable();
                $table->string('value_5')->nullable();
                $table->string('value_6')->nullable();
                $table->string('value_7')->nullable();
                $table->string('value_8')->nullable();
                $table->string('value_9')->nullable();
                $table->string('value_10')->nullable();
            });

            return true;
        } else {
            return false;
        }
    }

    // (게시판) 관리자의 선택 복사, 이동에 필요한 파라미터
    public function getMoveParams($boardId, $request)
    {
        session()->put('writeIds',$request->chk_id);

        return [
            'boards' => Board::orderBy('group_id', 'desc')->orderBy('subject', 'desc')->get(),
            'currentBoard' => Board::find($boardId),
            'type' => $request->type,
        ];
    }
}
