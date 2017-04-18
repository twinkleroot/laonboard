@if(!is_null($type))
    <div>
        <table>
        <thead>
        <tr>
            <th scope="col">제목</th>
            <th scope="col">선택</th>
        </tr>
        </thead>
        <tbody>
            @if(count($results) > 0)
            @foreach($results as $result)
            <tr>
                <td>{{ $result['subject'] }}</td>
                <td>
                    <input type="hidden" name="subject[]" value="{{ preg_replace('/[\'\"]/', '', $result['subject']) }}">
                    <input type="hidden" name="link[]" value=
                        @if($type == 'group') "http://example.com"
                        @elseif($type == 'board') {{ route('board.index', $result['id']) }}
                        @elseif($type == 'content') "http://content"
                        @endif
                    >
                    <button type="button" class="btn btn-primary add_select">선택</button>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
        </table>
    </div>

    <div class="btn_win02 btn_win">
        <button type="button" class="btn_cancel" onclick="window.close();">창닫기</button>
    </div>
@else
    <div>
        <table>
            <tbody>
            <tr>
                <th><label for="name">메뉴</label></th>
                <td><input type="text" name="name" id="name" required></td>
            </tr>
            <tr>
                <th><label for="link">링크</label></th>
                <td>
                    링크는 http://를 포함해서 입력해 주세요.<br />
                    <input type="text" name="link" id="link" required>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_win02 btn_win">
        <button type="button" id="add_manual" class="btn btn-primary">추가</button>
        <button type="button" class="btn btn-cancel" onclick="window.close();">창닫기</button>
    </div>
@endif
