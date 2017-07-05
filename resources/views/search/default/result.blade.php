@extends('layouts.default.basic')

@section('title')
    전체검색 결과 | {{ cache("config.homepage")->title }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div id="board" class="container">
            <div class="bd_header">
                <div class="bd_head">
                    <span>전체검색 결과</span>
                </div>
            </div>

            <form class="bd_sch">
                <ul>
                    <li class="sch_slt">
                        <label for="" class="sr-only">게시판 그룹선택</label>
                        <select name="" id="">
                            <option>전체분류</option>
                            <option>그룹1</option>
                        </select>
                    </li>
                    <li class="sch_slt">
                        <label for="" class="sr-only">검색조건</label>
                        <select name="" id="">
                            <option value="">제목+내용</option>
                            <option value="">제목</option>
                            <option value="">내용</option>
                            <option value="">회원이메일</option>
                            <option value="">닉네임</option>
                        </select>
                    </li>
                    <li class="sch_kw">
                        <label for="" class="sr-only">검색어</label>
                        <input type="text" name="" value="" id="" class="search" required>
                        <button type="submit" id="" class="search-icon">
                            <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                        </button>
                    </li>
                    <li class="sch_chk">
                        <input type="radio" name="a" id="or"><label for="or">OR</label>
                        <input type="radio" name="a" id="and"><label for="and">AND</label>
                    </li>
                </ul>
            </form>

            <div id="sch_result">
                <section id="sch_res_ov">
                    <h2>[검색단어] 전체검색 결과 게시판</h2>
                    <dl>
                        <dt>게시판</dt>
                        <dd><strong class="sch_word">2개</strong></dd>
                        <dt>게시물</dt>
                        <dd><strong class="sch_word">3개</strong></dd>
                    </dl>
                    <p>1/1 페이지 열람 중</p>
                </section>

                <div class="sch_res_ctg">
                    <ul>
                        <li><a href="#">전체게시판</a></li>
                        <li><a href="#"><strong>게시판1</strong> <span class="count">1</span></a></li> <!-- 게시판 검색된 갯수에 맞춰 증가 -->
                        <li><a href="#"><strong>게시판4</strong> <span class="count">2</span></a></li>
                    </ul>
                </div>

                <!-- 검색결과가 없을 경우 -->
                <section id="sch_res_list">
                    <div class="sch_res_list_bd">
                        <span class="empty_table">
                            <i class="fa fa-exclamation-triangle"></i> 검색된 자료가 없습니다.
                        </span>
                    </div>
                </section>
                <!-- 검색결과가 없을 경우 end -->

                <!-- 게시판 -->
                <section id="sch_res_list">
                    <div class="sch_res_list_hd">
                        <span class="bdname">[게시판이름] 게시판 내 결과</span>
                        <span class="more">
                            <a href="#"><strong>[게시판이름]</strong> 결과 더보기<i class="fa fa-caret-right"></i></a>
                        </span>
                    </div>
                    <div class="sch_res_list_bd">
                        <ul>
                            <!-- 게시물 -->
                            <li class="contents">
                                <span class="sch_subject"><a href="#">게<span class="sch_key">시물</span> 제목</a> <a href="#">[새창으로 열기]</a></span>
                                <p>웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 <span class="sch_key">손제호</span>가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 관심과 애정을 받고 있다. 노블레스는 820년간의 긴 수면기를 보내고 새로운 세상에 눈을 뜬, 캐릭터의 이야기를 담고 있다. 현재 465화까지 업그레이드 됐다. 웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 손제호가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 …</p>
                                <span class="sv_wrap">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">작성자</a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">쪽지보내기</a></li>
                                        <li><a href="#">메일보내기</a></li>
                                        <li><a href="#">자기소개</a></li>
                                        <li><a href="#">전체게시물</a></li>
                                        <li><a href="#">회원정보변경</a></li>
                                        <li><a href="#">포인트내역</a></li>
                                    </ul>
                                </span>
                                <span class="sch_datetime">2017-07-04 09:18:42</span>
                            </li>
                            <!-- 게시물 END -->
                            <li class="contents">
                                <span class="sch_subject"><a href="#">게<span class="sch_key">시물</span> 제목</a> <a href="#">[새창으로 열기]</a></span>
                                <p>웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 <span class="sch_key">손제호</span>가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 관심과 애정을 받고 있다. 노블레스는 820년간의 긴 수면기를 보내고 새로운 세상에 눈을 뜬, 캐릭터의 이야기를 담고 있다. 현재 465화까지 업그레이드 됐다. 웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 손제호가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 …</p>
                                <span class="sv_wrap">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">작성자</a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">쪽지보내기</a></li>
                                        <li><a href="#">메일보내기</a></li>
                                        <li><a href="#">자기소개</a></li>
                                        <li><a href="#">전체게시물</a></li>
                                        <li><a href="#">회원정보변경</a></li>
                                        <li><a href="#">포인트내역</a></li>
                                    </ul>
                                </span>
                                <span class="sch_datetime">2017-07-04 09:18:42</span>
                            </li>
                        </ul>
                    </div>
                </section>
                <!-- 게시판 END -->

                <!-- 게시판 -->
                <section id="sch_res_list">
                    <div class="sch_res_list_hd">
                        <span class="bdname">[게시판이름] 게시판 내 결과</span>
                        <span class="more">
                            <a href="#"><strong>[게시판이름]</strong> 결과 더보기<i class="fa fa-caret-right"></i></a>
                        </span>
                    </div>
                    <div class="sch_res_list_bd">
                        <ul>
                            <!-- 게시물 -->
                            <li class="contents">
                                <span class="sch_subject"><a href="#">게<span class="sch_key">시물</span> 제목</a> <a href="#">[새창으로 열기]</a></span>
                                <p>웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 <span class="sch_key">손제호</span>가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 관심과 애정을 받고 있다. 노블레스는 820년간의 긴 수면기를 보내고 새로운 세상에 눈을 뜬, 캐릭터의 이야기를 담고 있다. 현재 465화까지 업그레이드 됐다. 웹툰 노블레스가 화제다. 노블레스가 포털 사이트에 등장했다. 등장한 이유는 노블레스가 화요웹툰이기 때문이다. 이 웹툰은 손제호가 쓰고 이광수가 그린 웹툰이다. 흥미로운 주제로 팬들의 …</p>
                                <span class="sv_wrap">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">작성자</a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">쪽지보내기</a></li>
                                        <li><a href="#">메일보내기</a></li>
                                        <li><a href="#">자기소개</a></li>
                                        <li><a href="#">전체게시물</a></li>
                                        <li><a href="#">회원정보변경</a></li>
                                        <li><a href="#">포인트내역</a></li>
                                    </ul>
                                </span>
                                <span class="sch_datetime">2017-07-04 09:18:42</span>
                            </li>
                            <!-- 게시물 END -->
                        </ul>
                    </div>
                </section>
                <!-- 게시판 END -->
            </div>
        </div>
    </div>
</div>
@endsection
