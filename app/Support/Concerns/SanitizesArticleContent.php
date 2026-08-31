<?php

namespace App\Support\Concerns;

use DOMDocument;
use DOMElement;

trait SanitizesArticleContent
{
    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! class_exists(DOMDocument::class)) {
            $text = htmlspecialchars(strip_tags($html), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');

            return '<p>'.nl2br($text, false).'</p>';
        }

        if (strlen($html) > self::MAX_BYTES) {
            $html = substr($html, 0, self::MAX_BYTES);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="UTF-8"><!doctype html><html><body><div id="article-content-root">'.$html.'</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('article-content-root');

        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->sanitizeChildren($root);

        $clean = '';

        foreach (iterator_to_array($root->childNodes) as $child) {
            $clean .= $document->saveHTML($child);
        }

        return trim($clean);
    }

    public function plainText(?string $html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    public function wordCount(?string $html): int
    {
        $text = $this->plainText($html);

        if ($text === '') {
            return 0;
        }

        preg_match_all('/[\p{L}\p{N}]+(?:[’\'\-][\p{L}\p{N}]+)*/u', $text, $matches);

        return count($matches[0] ?? []);
    }

    public function firstImageUrl(?string $html): ?string
    {
        if (! is_string($html) || trim($html) === '') {
            return null;
        }

        if (! class_exists(DOMDocument::class)) {
            return null;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $image = $document->getElementsByTagName('img')->item(0);

        return $image instanceof DOMElement
            ? $this->safeImageUrl($image->getAttribute('src'))
            : null;
    }

    public function imageUrl(mixed $url): ?string
    {
        return is_string($url) ? $this->safeImageUrl($url) : null;
    }
}
