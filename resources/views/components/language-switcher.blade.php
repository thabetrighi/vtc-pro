<a href="#" class="m-auto d-flex align-items-center btn btn-link position-relative px-1 py-0 h-100 dropdown-toggle"
    data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Change Language') }}">
    <x-orchid-icon path="bs.translate" width="1.1em" height="1.1em" />
</a>

<ul class="dropdown-menu">
    @foreach ($languages as $code => $name)
        <li>
            <a class="dropdown-item {{ $currentLanguage === $code ? 'active' : '' }}"
                href="{{ route('platform.change-language', ['lang' => $code]) }}">
                {{ $name }}
                @if ($currentLanguage === $code)
                    <x-orchid-icon path="bs.check2" width="1em" height="1em" class="ms-2" />
                @endif
            </a>
        </li>
    @endforeach
</ul>
