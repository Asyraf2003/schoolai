<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['articles', 'hero_settings', 'hero_slides'] as $name) {
            if (! Schema::hasTable('legacy_'.$name) && Schema::hasTable($name)) {
                Schema::rename($name, 'legacy_'.$name);
            }
        }
        if (! Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table): void {
                $table->id();
                $table->string('slug', 220)->unique('simple_articles_slug_unique');
                foreach (['id', 'en', 'ar'] as $locale) {
                    $table->string('title_'.$locale, 200)->nullable();
                    $table->text('description_'.$locale)->nullable();
                    $table->longText('content_'.$locale)->nullable();
                }
                $table->string('cover_url', 2048)->nullable();
                $table->date('published_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        DB::table('legacy_articles')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $slug = $row->slug ?: 'artikel-lama-'.$row->id;
                if (! $row->slug) {
                    $suffix = 1;
                    while (DB::table('legacy_articles')->where('slug', $slug)->exists()) {
                        $slug = 'artikel-lama-'.$row->id.'-'.++$suffix;
                    }
                }
                $values = [
                    'id' => $row->id,
                    'slug' => $slug,
                    'cover_url' => $row->thumbnail_url,
                    'published_date' => $row->published_date,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'deleted_at' => $row->deleted_at ?: (
                        $row->article_status === 'draft' || ($row->published_at && $row->published_at > now()->toDateTimeString())
                            ? now()->toDateTimeString() : null
                    ),
                ];
                foreach (['id', 'en', 'ar'] as $locale) {
                    $values['title_'.$locale] = $row->{'title_'.$locale};
                    $values['description_'.$locale] = $row->{'description_'.$locale};
                    $html = $row->{'content_'.$locale};
                    if (! $html && $row->article_source === 'external') {
                        $html = '<p>'.htmlspecialchars($row->{'description_'.$locale} ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</p>';
                        $link = $row->{'link_'.$locale};
                        if (is_string($link) && filter_var($link, FILTER_VALIDATE_URL)
                            && in_array(strtolower(parse_url($link, PHP_URL_SCHEME) ?? ''), ['https', 'http'], true)) {
                            $html .= '<p><a href="'.htmlspecialchars($link, ENT_QUOTES, 'UTF-8').'">'
                                .htmlspecialchars($row->{'title_'.$locale} ?: $link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</a></p>';
                        }
                    }
                    $values['content_'.$locale] = $this->textOnly($html);
                }
                DB::table('articles')->insertOrIgnore($values);
            }
        });
        if (DB::table('articles')->count() !== DB::table('legacy_articles')->count()) {
            throw new RuntimeException('Article migration is incomplete; original records remain in legacy_articles.');
        }
    }

    private function textOnly(?string $html): ?string
    {
        if (! $html) {
            return null;
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($dom);
        foreach (iterator_to_array($xpath->query('//script|//style|//img|//iframe|//video|//audio|//object|//embed|//svg|//canvas')) as $node) {
            $node->parentNode?->removeChild($node);
        }
        foreach (iterator_to_array($dom->getElementsByTagName('*')) as $node) {
            $href = $node->tagName === 'a' ? $node->getAttribute('href') : '';
            foreach (iterator_to_array($node->attributes) as $attribute) {
                $node->removeAttribute($attribute->name);
            }
            if (filter_var($href, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($href, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true)) {
                $node->setAttribute('href', $href);
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }
        $body = $dom->getElementsByTagName('body')->item(0);
        $result = '';
        foreach ($body?->childNodes ?? [] as $node) {
            $result .= $dom->saveHTML($node);
        }

        return strip_tags($result, '<p><br><strong><b><em><i><u><s><h2><h3><blockquote><ul><ol><li><a><code><pre><hr>');
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only content migration: originals are archived; rollback must preserve new content.');
    }
};
