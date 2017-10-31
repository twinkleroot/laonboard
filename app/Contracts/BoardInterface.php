<?php

namespace App\Contracts;

interface BoardInterface
{
    // 게시판 그룹 모델과의 관계 설정
    public function group();

    // 게시판 모델을 가져 온다. static 변수의 글 정보가 같으면 질의하지 않고 static 변수를 리턴 시킴
    public static function getBoard($boardName, $key='id');

    // (게시판 관리) index 페이지에서 필요한 파라미터 가져오기
    public function getBoardIndexParams($request);

    // (게시판 관리) create 페이지에서 필요한 파라미터 가져오기
    public function getBoardCreateParams($request);

    // (게시판 관리) board 테이블에 새 게시판 행 추가
    public function storeBoard($data);

    // (게시판 관리) edit 페이지에서 필요한 파라미터 가져오기
    public function getBoardEditParams($request, $id);

    // (게시판 관리) 정보 수정
    public function updateBoard($data, $id);

    // (게시판 관리) 그룹 적용, 전체 적용
    public function applyBoard($data, $prefix);

    // (게시판 관리) 게시판 구조 복사
    public function copyBoard($data);

    // (게시판 관리) 선택 삭제
    public function deleteBoards($ids);

    // (게시판 관리) 선택 수정
    public function selectedUpdate($request);

    // (게시판 관리 -> 게시판 추가) 새 게시판 테이블 생성
    public function createWriteTable($tableName);

    // 게시판 썸네일 삭제
    public function deleteThumbnail($dirName, $boardName);

}
