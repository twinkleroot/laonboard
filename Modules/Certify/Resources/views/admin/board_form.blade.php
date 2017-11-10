<tr>
    <th>본인확인 사용</th>
    <td class="table_body">
        <select name="use_cert" class="form-control form_large">
            <option value="not-use" @if($type == 'edit' && $board->use_cert == 'not-use') selected @elseif($type != 'edit') selected @endif>사용안함</option>
            @if(cache('config.cert')->certUse)
            <option value="cert" @if($type == 'edit' && $board->use_cert == 'cert') selected @endif>본인확인된 회원전체</option>
            <option value="adult" @if($type == 'edit' && $board->use_cert == 'adult') selected @endif>본인확인된 성인회원만</option>
            <option value="hp-cert" @if($type == 'edit' && $board->use_cert == 'hp-cert') selected @endif>휴대폰 본인확인된 회원전체</option>
            <option value="hp-adult" @if($type == 'edit' && $board->use_cert == 'hp-adult') selected @endif>휴대폰 본인확인된 성인회원만</option>
            @endif
        </select>
        <span class="help-block">본인확인 여부에 따라 게시물을 조회 할 수 있도록 합니다.</span>
    </td>
    <td class="table_chk">
        <input type="checkbox" id="chk_group_use_cert" name="chk_group_use_cert" value="1" />
        <label for="chk_group_use_cert">그룹적용</label>
        <input type="checkbox" id="chk_all_use_cert" name="chk_all_use_cert" value="1" />
        <label for="chk_all_use_cert">전체적용</label>
    </td>
</tr>
