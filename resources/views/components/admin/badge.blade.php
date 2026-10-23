@props(['tone' => 'neutral'])
<span {{ $attributes->class(['admin-badge', 'admin-badge--'.$tone]) }}>{{ $slot }}</span>
