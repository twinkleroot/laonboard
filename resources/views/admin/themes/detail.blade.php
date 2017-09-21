<div id="theme_detail">
    <div class="thdt_img"><img src="{{ ver_asset("themes/$theme/images/screenshot.png") }}"></div>
    <div class="thdt_if">
        <button type="button" class="close_btn">닫기</button>
        @if($info['themeUri'])
        <a href="{{ $info['themeUri'] }}" target="_blank">
            <h2>{{ $info['themeName'] }}<i class="thdt_icon fa fa-home" aria-hidden="true"></i></h2>
        </a>
        @else
            <h2>{{ $info['themeName'] }}<i class="thdt_icon fa fa-home" aria-hidden="true"></i></h2>
        @endif
        <table>
            <tbody>
                <tr>
                    <th scope="row">Version</th>
                    <td>{{ $info['version'] }}</td>
                </tr>
                <tr>
                    <th scope="row">Maker</th>
                    <td>
                        @if(isset($info['makerUri']))
                            <a href="{{ $info['makerUri'] }}" target="_blank" class="thdt_home">{{ $info['maker'] }}<i class="thdt_icon fa fa-home" aria-hidden="true"></i></a>
                        @else
                            {{ $info['maker'] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th scope="row">License</th>
                    <td>
                        @if(isset($info['licenseUri']))
                            <a href="{{ $info['licenseUri'] }}" target="_blank" class="thdt_home">{{ $info['license'] }}<i class="thdt_icon fa fa-home" aria-hidden="true"></i></a>
                        @else
                            {{ $info['license'] }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <p>{{ $info['detail'] }}</p>
    </div>
</div>

<script>
$(".close_btn").on("click", function() {
    $("#theme_detail").remove();
});
</script>
