<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">
    <header class="stories__header">
        <h2 class="stories__heading" id="stories-title" data-stories-heading aria-label="{{ __('stories.title') }}">
            @foreach (__('stories.lines') as $line)
                <span class="stories__heading-clip"><span data-stories-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
</section>
