<li>
    <a href="{{ route('inform') }}">
        알림 <span class="memocount">{{ auth()->user()->unreadNotifications->count() }}</span>
    </a>
</li>
