<div class="btn-group" role="group">
    @foreach (['en', 'ar', 'fr'] as $lang)
        <a href="{{ route('platform.language-manager', ['lang' => $lang]) }}"
            class="btn btn-secondary {{ request('lang') === $lang ? 'active' : '' }}">
            {{ strtoupper($lang) }}
        </a>
    @endforeach
</div>
