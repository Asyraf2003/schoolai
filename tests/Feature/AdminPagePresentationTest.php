<?php

it('keeps all H5 group four Blade owners free of PHP shaping', function (): void {
    $files = [
        resource_path('views/admin/ppdb/edit.blade.php'),
        resource_path('views/admin/ppdb/edit/showcase-list.blade.php'),
        resource_path('views/admin/hero/index.blade.php'),
        resource_path('views/admin/site-statistics/edit.blade.php'),
        resource_path('views/admin/site-statistics/edit/statistics-list.blade.php'),
        resource_path('views/admin/gallery/form.blade.php'),
        resource_path('views/admin/gallery/page-sections/form.blade.php'),
        resource_path('views/admin/gallery/page-sections/show.blade.php'),
        resource_path('views/admin/gallery/show.blade.php'),
        resource_path('views/admin/gallery/index.blade.php'),
        resource_path('views/admin/gallery/index/page-sections.blade.php'),
        resource_path('views/admin/gallery/index/homepage-items.blade.php'),
        resource_path('views/admin/placeholder.blade.php'),
        resource_path('views/admin/articles/form.blade.php'),
        resource_path('views/admin/articles/index.blade.php'),
        resource_path('views/admin/testimonials/form.blade.php'),
    ];

    foreach ($files as $file) {
        $source = file_get_contents($file);

        expect($source, basename($file))
            ->not->toContain('@php')
            ->not->toContain('@endphp')
            ->not->toContain('<?php');
    }
});

it('registers exact view owners for every group four presentation boundary', function (): void {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $registrations = [
        "View::composer('admin.articles.form', AdminArticleFormComposer::class)",
        "View::composer('admin.articles.index', AdminArticleIndexComposer::class)",
        "View::composer('admin.gallery.form', AdminGalleryFormComposer::class)",
        "View::composer('admin.gallery.index', AdminGalleryIndexComposer::class)",
        "View::composer('admin.gallery.page-sections.form', AdminGallerySectionFormComposer::class)",
        "View::composer('admin.gallery.show', AdminGalleryShowComposer::class)",
        "View::composer('admin.placeholder', AdminPlaceholderComposer::class)",
        "View::composer('admin.ppdb.edit', AdminPpdbEditComposer::class)",
        "View::composer('admin.site-statistics.edit', AdminSiteStatisticsEditComposer::class)",
        "View::composer('admin.testimonials.form', AdminTestimonialFormComposer::class)",
    ];

    foreach ($registrations as $registration) {
        expect($provider)->toContain($registration);
    }
});
