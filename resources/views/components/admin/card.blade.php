@props(['title' => null])
<section {{ $attributes->class(['admin-card']) }}>
    @if($title)<h2 class="admin-card-title">{{ $title }}</h2>@endif
    {{ $slot }}
</section>
