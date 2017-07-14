$(function() {
    $(".use_apply").on("click", function() {
        var theme = $(this).data("theme");
        var name  = $(this).data("name");

        if(!confirm(name+" 테마를 적용하시겠습니까?\n\n 테마에서 지정된 스킨으로 모든 스킨이 변경됩니다."))
            return false;

        // var set_default_skin = 0;
        // if($(this).data("set_default_skin") == true) {
        //     if(confirm("기본환경설정, 1:1문의 스킨을 테마에서 설정된 스킨으로 변경하시겠습니까?\n\n변경을 선택하시면 테마에서 지정된 스킨으로 회원스킨 등이 변경됩니다."))
        //         set_default_skin = 1;
        // }

        $.ajax({
            type: "POST",
            url: "theme/update",
            data: {
                "theme": theme,
                "_token": window.Laravel.csrfToken
                // "set_default_skin": set_default_skin
            },
            cache: false,
            async: false,
            success: function(data) {
                if(data) {
                    alert(data);
                    return false;
                }

                document.location.reload();
            }
        });
    });

    $(".theme_preview").on("click", function() {
        var theme = $(this).data("theme");

        $("#theme_detail").remove();

        $.ajax({
            type: "POST",
            url: "theme/detail",
            data: {
                "theme": theme,
                "_token": window.Laravel.csrfToken
            },
            cache: false,
            async: false,
            success: function(data) {
                $(".theme_list").after(data);
            }
        });
    });
});
