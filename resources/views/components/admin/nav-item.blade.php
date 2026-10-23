@props(['route', 'icon', 'active' => false])
<a href="{{ route($route) }}" {{ $attributes->class(['admin-nav-link', 'is-active' => $active]) }} @if($active) aria-current="page" @endif>
    <x-admin.icon :name="$icon"/>
    <span>{{ $slot }}</span>
</a>
