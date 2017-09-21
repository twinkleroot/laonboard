// 임시 저장하는 시간을 초단위로 설정한다.
var AUTOSAVE_INTERVAL = 60; // 초

// 글의 제목과 내용을 바뀐 부분이 있는지 비교하기 위하여 저장해 놓는 변수
var saveSubject = null;
var saveContent = null;

function autosave() {
    $("form#fwrite").each(function() {

        if(tinymce.activeEditor) {
            this.content.value = tinymce.activeEditor.getContent();
        }

        // 변수에 저장해 놓은 값과 다를 경우에만 임시 저장함
        if (saveSubject != this.subject.value || saveContent != this.content.value) {
            $.ajax({
                url: "/autosave",
                data: {
                    "uid" : this.uid.value,
                    "subject": this.subject.value,
                    "content": this.content.value,
                    "_token": this._token.value,
                },
                type: "POST",
                success: function(responseData, textStatus, jqXHR){
                    if (responseData) {
                        $("#autosaveCount").text('(' + responseData + ')');
                    }
                }
            });
            saveSubject = this.subject.value;
            saveContent = this.content.value;
        }
    });
}

$(function(){

    setInterval(autosave, AUTOSAVE_INTERVAL * 1000);

    // 임시저장된 글목록을 가져옴
    $('#autosaveBtn').click(function(){
        if ($('#autosavePop').is(":hidden")) {
            $.ajax({
                url: '/autosave',
                type: 'get',
                datatype: 'json',
                success: function(data) {
                    $('#autosavePop').empty();
                    for(var i=0; i<data.length; i++) {
                        $('#autosavePop')
                            .append("<li>"
                                    + "<a href='#' class='autosaveLoad'><span>"
                                    + data[i].subject + "</span>"
                                    + "<span class='sv-date'>"
                                    + data[i].created_at + "</span></a>"
                                    + "<a href='#' class='save-delete'><i class='fa fa-times'></i></a></li>")
                            .find('li:eq('+i+')')
                            .data({ id: data[i].id, uid: data[i].unique_id });
                    }
                }
            });
            $("#autosavePop").show();
        } else {
            $("#autosavePop").hide();
        }

    });

    // 임시저장된 글 제목과 내용을 가져와서 제목과 내용 입력박스에 노출해 줌
    $(document).on( "click", ".autosaveLoad", function(){
        var li = $(this).parents("li");
        var id = li.data("id");
        var uid = li.data("uid");
        $("#uid").val(uid);

        $.get("/autosave/"+id, function(data){
            var subject = data.subject;
            var content = data.content;
            $("#subject").val(subject);

            if(tinymce) {
                $("#content").val(tinymce.activeEditor.setContent(content));
            } else {
                $("#content").val(content);
            }
        }, "json");

        $("#autosavePop").hide();
    });

    $(document).on("click", ".save-delete", function(){
        var li = $(this).parents("li");

        $.ajax({
            url: '/autosave/' + li.data('id'),
            type: 'post',
            data: { '_method' : 'delete', '_token' : $("input[name='_token']").val() },
            datatype: 'json',
            success: function(data) {
                if (data == -1) {
                    alert("임시 저장된글을 삭제중에 오류가 발생하였습니다.");
                } else {
                    $("#autosaveCount").text('(' + data + ')');
                    li.remove();
                }
            }
        });
    });

});
