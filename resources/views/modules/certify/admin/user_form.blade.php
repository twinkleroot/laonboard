<section id="mb_cert" class="adm_section">
    <div class="adm_box_hd">
        <span class="adm_box_title">본인인증</span>
    </div>
    <table class="adm_box_table">
        <tr>
            <th><label for="certify_case">본인확인방법</label></th>
            <td class="table_body">
                {{-- <input type="radio" name="certify" id="certify_case_ipin" value="ipin" @if($user->certify == 'ipin') checked @endif />
                    <label for="certify_case_ipin">아이핀</label> --}}
                <input type="radio" name="certify" id="certify_case_hp" value="hp" @if(isset($user) && $user->certify == 'hp') checked @endif />
                <label for="certify_case_hp">휴대폰</label>
            </td>
        </tr>
        <tr>
            <th><label for="certify">본인확인</th>
            <td class="table_body">
                <input type="radio" name="certify_signal" id="certify_yes" @if(isset($user) && $user->certify) checked @endif value="1" />
                <label for="certify_yes">예</label>
                <input type="radio" name="certify_signal" id="certify_no" @if(isset($user) && (!$user->certify || empty($user->certify)) ) checked @endif value="0" />
                <label for="certify_no">아니오</label>
            </td>
        </tr>
        <tr>
            <th><label for="adult">성인인증</th>
            <td class="table_body">
                <input type="radio" name="adult" id="adult_yes" @if(isset($user) && $user->adult) checked @endif value="1" />
                <label for="adult_yes">예</label>
                <input type="radio" name="adult" id="adult_no" @if(isset($user) && (!$user->adult || empty($user->adult)) ) checked @endif value="0" />
                <label for="adult_no">아니오</label>
            </td>
        </tr>
    </table>
</section>
