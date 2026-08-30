@once
  @vite('resources/css/pages/welcome-mega-menu.css')
@endonce

<span class="nav-language__flag nav-language__flag--{{ $flagLocale }}" aria-hidden="true">
  @switch($flagLocale)
    @case('en')
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="60" fill="#21468b" />
        <path d="M0 0 60 60M60 0 0 60" stroke="#fff" stroke-width="13" />
        <path d="M0 0 60 60M60 0 0 60" stroke="#cf142b" stroke-width="6" />
        <path d="M30 0v60M0 30h60" stroke="#fff" stroke-width="18" />
        <path d="M30 0v60M0 30h60" stroke="#cf142b" stroke-width="10" />
      </svg>
      @break

    @case('ar')
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="20" fill="#159447" />
        <rect y="20" width="60" height="20" fill="#fff" />
        <rect y="40" width="60" height="20" fill="#111" />
        <rect width="16" height="60" fill="#d8232a" />
      </svg>
      @break

    @default
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="30" fill="#e70011" />
        <rect y="30" width="60" height="30" fill="#fff" />
      </svg>
  @endswitch
</span>
