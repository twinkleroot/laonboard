<div id="theme_detail">
    <div class="thdt_img"><img src="{{ asset("images/screenshot_$theme.png") }}"></div>
    <div class="thdt_if">
        <h2>{{ $info['themeName'] }}</h2>
        <table>
            <tr>
                <th scope="row">Version</th>
                <td>{{ $info['version'] }}</td>
            </tr>
            <tr>
                <th scope="row">Maker</th>
                <td>
                    @if($info['makerUri'])
                        <a href="{{ $info['makerUri'] }}" target="_blank" class="thdt_home">{{ $info['maker'] }}</a>
                    @else
                        {{ $info['maker'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th scope="row">License</th>
                <td>
                    @if($info['licenseUri'])
                        <a href="{{ $info['licenseUri'] }}" target="_blank" class="thdt_home">{{ $info['license'] }}</a>
                    @else
                        {{ $info['license'] }}
                    @endif
                </td>
            </tr>
        </table>
        <p>{{ $info['detail'] }}</p>
        <button type="button" class="close_btn">닫기</button>
    </div>
</div>

<script>
$(".close_btn").on("click", function() {
    $("#theme_detail").remove();
});
</script>
