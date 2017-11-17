function alertclose() {
    $("#adm_save").remove();
}

$(document).ready(function($){
    var nav = $('#body_tab_type2');

    $(window).scroll(function () {
        if ($(this).scrollTop() > 175) {
            nav.addClass("f-tab");
        } else {
            nav.removeClass("f-tab");
        }
    });

    $("a[id='" + menuVal+ "']").parent().parent().show();
    $("a[id='" + menuVal+ "']").css('background', '#616161');

    $('#showmenu').click(function() {
        var hidden = $('.sidebarmenu').data('hidden');
        if(hidden){
            $('.sidebarmenu').animate({
                left: '0px'
            },300),
            $('.sidebarmenu2').animate({
                left: '230px'
            },300)
        } else {
            $('.sidebarmenu').animate({
                left: '-230px'
            },300),
            $('.sidebarmenu2').animate({
                left: '0px'
            },300)
        }
        $('.sidebarmenu,.image').data("hidden", !hidden);
    });

    $('a.sd_1depth').click(function() {
        $(this).parent().next('.sd_2depth').toggle(200);
        return false;
    });

    $(".upbtn").hide(); //top버튼
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
            $('.upbtn').fadeIn();
            } else {
            $('.upbtn').fadeOut();
            }
        });
        $('.upbtn a').click(function () {
            $('body,html').animate({
            scrollTop: 0
            }, 500);
            return false;
        });
    });
});
