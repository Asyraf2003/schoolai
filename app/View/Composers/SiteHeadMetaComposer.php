<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SiteHeadMetaComposer
{
    public function __construct(
        private Request $request,
        private Translator $translator,
    ) {}

    public function compose(View $view): void
    {
        $viewData = $view->getData();
        $headSiteName = 'Al Mustaqbal School';
        $headTitle = trim((string) (
            $viewData['pageTitle'] ?? $headSiteName
        ));
        $headDescription = trim((string) (
            $viewData['pageDescription'] ?? ''
        ));
        $headCanonicalUrl = $this->request->url();
        $headHomeUrl = route('home');
        $headLogoUrl = asset('media/home/logo.png');
        $headImageUrl = asset('media/home/og-home.jpg');
        $locale = $this->translator->getLocale();
        $headLanguage = in_array($locale, ['id', 'en', 'ar'], true)
            ? $locale
            : 'id';
        $headDirection = $headLanguage === 'ar' ? 'rtl' : 'ltr';
        $headLocale = match ($headLanguage) {
            'en' => 'en_US',
            'ar' => 'ar_AR',
            default => 'id_ID',
        };
        $headImageAlt = $this->translator->get('shared.head.image_alt');
        $headStructuredData = $this->structuredData(
            $headSiteName,
            $headTitle,
            $headDescription,
            $headCanonicalUrl,
            $headHomeUrl,
            $headLogoUrl,
            $headImageUrl,
            $headImageAlt,
            $headLanguage,
        );

        $view->with(compact(
            'headSiteName',
            'headTitle',
            'headDescription',
            'headCanonicalUrl',
            'headImageUrl',
            'headLanguage',
            'headDirection',
            'headLocale',
            'headImageAlt',
            'headStructuredData',
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function structuredData(
        string $siteName,
        string $title,
        string $description,
        string $canonicalUrl,
        string $homeUrl,
        string $logoUrl,
        string $imageUrl,
        string $imageAlt,
        string $language,
    ): array {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'School',
                    '@id' => $homeUrl.'#school',
                    'name' => $siteName,
                    'url' => $homeUrl,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoUrl,
                        'contentUrl' => $logoUrl,
                        'width' => 1080,
                        'height' => 1080,
                    ],
                    'image' => ['@id' => $imageUrl.'#primaryimage'],
                ],
                [
                    '@type' => 'ImageObject',
                    '@id' => $imageUrl.'#primaryimage',
                    'url' => $imageUrl,
                    'contentUrl' => $imageUrl,
                    'caption' => $imageAlt,
                    'width' => 1200,
                    'height' => 630,
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $homeUrl.'#website',
                    'url' => $homeUrl,
                    'name' => $siteName,
                    'inLanguage' => ['id', 'en', 'ar'],
                    'publisher' => ['@id' => $homeUrl.'#school'],
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl.'#webpage',
                    'url' => $canonicalUrl,
                    'name' => $title,
                    'description' => $description,
                    'inLanguage' => $language,
                    'isPartOf' => ['@id' => $homeUrl.'#website'],
                    'about' => ['@id' => $homeUrl.'#school'],
                    'primaryImageOfPage' => [
                        '@id' => $imageUrl.'#primaryimage',
                    ],
                ],
            ],
        ];
    }
}
