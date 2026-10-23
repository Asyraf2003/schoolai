@props(['label', 'hint' => null])
<label {{ $attributes->class(['admin-field']) }}>
    <span class="admin-field-label">{{ $label }}</span>
    {{ $slot }}
    @if($hint)<span class="admin-field-hint">{{ $hint }}</span>@endif
</label>
