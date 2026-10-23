@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])
@if($href)
    <a href="{{ $href }}" {{ $attributes->class(['admin-button', 'admin-button--'.$variant]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['admin-button', 'admin-button--'.$variant]) }}>{{ $slot }}</button>
@endif
