@section('include_css')
    <!-- 팝업레이어용 CSS파일 -->
    <link rel="stylesheet" type="text/css" href="../css/popuplayer.css">
@endsection

@section('include_script')
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/common.js"></script>
@endsection

<div id="popuplayer">
    <h2>팝업레이어 알림</h2>
    @foreach($popups as $popup)
        @if(isset($_COOKIE["hd_pops_". $popup->id]))

        @else
        <div id="hd_pops_{{ $popup->id }}" class="hd_pops" style="top:{{ $popup->top }}px;left:{{ $popup->left }}px">
            <div class="hd_pops_con" style="width:{{ $popup->width }}px;height:{{ $popup->height }}px">
                {!! $popup->content !!}
            </div>
            <div class="hd_pops_footer">
                <button class="hd_pops_reject hd_pops_{{ $popup->id }} {{ $popup->disable_hours }}"><strong>24</strong>시간 동안 다시 열람하지 않습니다.</button>
                <button class="hd_pops_close hd_pops_{{ $popup->id }}">닫기</button>
            </div>
        </div>
        @endif
    @endforeach
</div>

<script>
$(function() {
    $(".hd_pops_reject").click(function() {
        var id = $(this).attr('class').split(' ');
        var ck_name = id[1];
        var exp_time = parseInt(id[2]);
        $("#"+id[1]).css("display", "none");
        set_cookie(ck_name, 1, exp_time, "{{ env('APP_URL') }}");
    });
    $('.hd_pops_close').click(function() {
        var idb = $(this).attr('class').split(' ');
        $('#'+idb[1]).css('display','none');
    });
    $("#header").css("z-index", 1000);
});
</script>
