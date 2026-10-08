<section class="values" id="nilai" data-values aria-labelledby="values-title">
    @include('landing.values-line')
    <header class="values__header">
        <h2 class="values__heading" id="values-title" data-values-heading aria-label="{{ $values['heading'] }}">
            @foreach ($values['heading_lines'] as $line)
                <span class="values__heading-clip"><span data-values-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
</section>
