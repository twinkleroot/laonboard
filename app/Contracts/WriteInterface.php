<?php

namespace App\Contracts;

interface WriteInterface
{
    // 글을 가져 온다. static 변수의 글 정보가 같으면 질의하지 않고 static 변수를 리턴 시킴
    public static function getWrite($boardId, $writeId, $id='id');

    // write 모델의 테이블 이름을 지정
    public function setTableName($tableName);

    // User 모델과의 관계
    public function user();

    // (게시판) index 페이지에서 필요한 파라미터 가져오기
    public function getIndexParams($writeModel, $request);

    // (게시판 리스트) 해당 커뮤니티 게시판 모델을 가져온다. (검색 포함)
    public function getWrites($writeModel, $request, $kind, $keyword, $currenctCategory);

    // 수동 페이징
    public function customPaging($request, $query, $sortField);

    // 글 보기 파라미터
    public function getViewParams($writeModel, $writeId, $request);

    // 조회수 증가
    public function increaseHit($writeModel, $write);

    // 소비성 포인트 계산(글 읽기, 파일 다운로드)
    public function calculatePoint($write, $request, $type);

    // 이전 글, 다음 글 경로, 제목 가져오기
    public function getPrevNextView($writeModel, $writeId, $request);

    // 이전 or 다음 글 url을 만든다.
    public function getPrevNextUrl($request, $write);

    // 글 읽기 중 링크 연결
    public function beforeLink($writeModel, $writeId, $linkNo);

    // 링크 연결수 증가
    public function increaseLinkHit($write, $linkNo);

    // (게시판) 글 쓰기 파라미터
    public function getCreateParams($request);

    // 글 수정 파라미터
    public function getEditParams($writeId, $writeModel, $request);

    // 답변 글 파라미터
    public function getReplyParams($writeId, $writeModel, $request);

    // (게시판) 글 쓰기 -> 저장
    public function storeWrite($writeModel, $request);

    // 글 수정
    public function updateWrite($writeModel, $request, $writeId, $file);

    // 게시글 삭제하면서 게시글에 종속된 것들도 함께 삭제
    public function deleteWriteCascade($writeModel, $writeId);

    // 글 삭제 - 게시글 삭제
    public function deleteWrite($writeModel, $writeId);

}
