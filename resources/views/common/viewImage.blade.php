<html>
<head>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script>
$(function() {
    var is_draggable = false;
    var x = y = 0;
    var pos_x = pos_y = 0;

    $(".draggable").mousemove(function(e) {
        if(is_draggable) {
            x = parseInt($(this).css("left")) - (pos_x - e.pageX);
            y = parseInt($(this).css("top")) - (pos_y - e.pageY);

            pos_x = e.pageX;
            pos_y = e.pageY;

            $(this).css({ "left" : x, "top" : y });
        }

        return false;
    });

    $(".draggable").mousedown(function(e) {
        pos_x = e.pageX;
        pos_y = e.pageY;
        is_draggable = true;
        return false;
    });

    $(".draggable").mouseup(function() {
        is_draggable = false;
        return false;
    });

    $(".draggable").dblclick(function() {
        window.close();
    });
});
</script>
</head>
<body>
    <div>
        <img src="/storage/{{ $imagePath }}" width="{{ $width }}" height="{{ $height }}" class="draggable"
             style="position: relative; top: 0px; left: 0px; cursor: move;" />
    </div>
</body>
</html>
