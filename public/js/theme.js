$(function() {
    $(".use_apply").on("click", function() {
        var theme = $(this).data("theme");
        var name  = $(this).data("name");

        if(!confirm(name+" 테마를 적용하시겠습니까?\n\n 테마에서 지정된 스킨으로 모든 스킨이 변경됩니다."))
            return false;

        $.ajax({
            type: "POST",
            url: "themes/update",
            data: {
                "theme": theme,
                "_token": window.Laravel.csrfToken
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
            url: "themes/detail",
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
