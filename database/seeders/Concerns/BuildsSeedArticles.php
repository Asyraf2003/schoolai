<?php

namespace Database\Seeders\Concerns;

use App\Models\Article;
use Illuminate\Database\Seeder;

trait BuildsSeedArticles
{
    /**
     * @param  array<int, string>  $tags
     * @param  array<int, string>  $contentId
     * @param  array<int, string>  $contentEn
     * @param  array<int, string>  $contentAr
     * @return array<string, mixed>
     */
    private function article(
        string $slug,
        string $titleId,
        string $titleEn,
        string $titleAr,
        string $descriptionId,
        string $descriptionEn,
        string $descriptionAr,
        array $tags,
        string $thumbnail,
        array $contentId,
        array $contentEn,
        array $contentAr,
    ): array {
        $contentIdHtml = $this->paragraphs($contentId);
        $contentEnHtml = $this->paragraphs($contentEn);
        $contentArHtml = $this->paragraphs($contentAr);

        return [
            'slug' => $slug,

            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,

            'subtitle_id' => $descriptionId,
            'subtitle_en' => $descriptionEn,
            'subtitle_ar' => $descriptionAr,

            'description_id' => $descriptionId,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,

            'content_id' => $contentIdHtml,
            'content_en' => $contentEnHtml,
            'content_ar' => $contentArHtml,

            'tags' => $tags,
            'word_count' => $this->wordCount($contentIdHtml),
            'thumbnail_url' => $thumbnail,

            'link_id' => url('/artikel/'.$slug),
            'link_en' => url('/artikel/'.$slug),
            'link_ar' => url('/artikel/'.$slug),
        ];
    }

    /** @param array<int, string> $paragraphs */
    private function paragraphs(array $paragraphs): string
    {
        return implode('', array_map(
            static fn (string $paragraph): string => '<p>'.e($paragraph).'</p>',
            $paragraphs,
        ));
    }

    private function wordCount(string $html): int
    {
        $text = trim(strip_tags($html));

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }
}
