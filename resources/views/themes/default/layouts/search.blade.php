<form class="hd_sch" name="searchBox" method="get" action="{{ route('search')}}">
    <input type="hidden" name="kind" value="subject||content" />
    <fieldset>
        <legend>사이트 내 전체검색</legend>
        <label for="keyword" class="sr-only"><strong>검색어 필수</strong></label>
        <input type="text" name="keyword" id="keyword" maxlength="20" required>
        <input type="submit" id="searchSubmit" value="검색">
    </fieldset>
    <input type="hidden" name="operator" value="and" />
</form>
