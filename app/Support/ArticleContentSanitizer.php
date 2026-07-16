<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

final class ArticleContentSanitizer
{
    private const MAX_BYTES = 2_000_000;

    private const ALLOWED_TAGS = [
        'a', 'blockquote', 'br', 'code', 'div', 'em', 'figcaption', 'figure',
        'h2', 'h3', 'hr', 'iframe', 'img', 'li', 'ol', 'p', 'pre', 'strong', 'ul',
    ];

    private const DROP_WITH_CONTENT = [
        'applet', 'audio', 'canvas', 'embed', 'form', 'input', 'link', 'math',
        'meta', 'object', 'script', 'style', 'svg', 'template', 'textarea', 'video',
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
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

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($node);
                continue;
            }

            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $parent->removeChild($node);
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                $this->sanitizeChildren($node);

                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
                continue;
            }

            $this->sanitizeElement($node, $tag);

            if ($node->parentNode !== null) {
                $this->sanitizeChildren($node);
            }
        }
    }

    private function sanitizeElement(DOMElement $element, string $tag): void
    {
        $original = [];
        $attributes = [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $attributes[] = $attribute->name;
            $original[$attribute->name] = $attribute->value;
        }

        foreach ($attributes as $attribute) {
            $element->removeAttribute($attribute);
        }

        if ($tag === 'a') {
            $href = $this->safeLinkUrl((string) ($original['href'] ?? ''));

            if ($href !== null) {
                $element->setAttribute('href', $href);
                $element->setAttribute('rel', 'noopener noreferrer');

                if (str_starts_with($href, 'http')) {
                    $element->setAttribute('target', '_blank');
                }
            }

            return;
        }

        if ($tag === 'img') {
            $source = $this->safeImageUrl((string) ($original['src'] ?? ''));

            if ($source === null) {
                $element->parentNode?->removeChild($element);
                return;
            }

            $element->setAttribute('src', $source);
            $element->setAttribute('alt', mb_substr(trim((string) ($original['alt'] ?? '')), 0, 300));
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('decoding', 'async');

            return;
        }

        if ($tag === 'iframe') {
            $source = $this->safeEmbedUrl((string) ($original['src'] ?? ''));

            if ($source === null) {
                $element->parentNode?->removeChild($element);
                return;
            }

            $element->setAttribute('src', $source);
            $element->setAttribute('title', mb_substr(trim((string) ($original['title'] ?? '')) ?: 'Media artikel', 0, 160));
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('allowfullscreen', 'allowfullscreen');
            $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');

            return;
        }

        $class = $this->allowedClass($tag, (string) ($original['class'] ?? ''));

        if ($class !== null) {
            $element->setAttribute('class', $class);
        }
    }

    private function allowedClass(string $tag, string $class): ?string
    {
        $allowed = match ($tag) {
            'figure' => ['article-image--inline', 'article-image--outset', 'article-image--screen'],
            'blockquote' => ['article-quote', 'article-pull-quote'],
            'p' => ['has-drop-cap'],
            'div' => ['article-embed', 'article-video'],
            default => [],
        };

        foreach (preg_split('/\s+/', trim($class)) ?: [] as $candidate) {
            if (in_array($candidate, $allowed, true)) {
                return $candidate;
            }
        }

        return null;
    }

    private function safeLinkUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            $path = strtolower((string) parse_url($url, PHP_URL_PATH));

            return str_starts_with($path, '/admin') || str_starts_with($path, '/login')
                ? null
                : $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function safeImageUrl(string $url): ?string
    {
        $url = trim($url);

        if (preg_match('~^/storage/articles/content/[A-Za-z0-9/_\-.]+$~', $url) === 1) {
            return $url;
        }

        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $scheme === 'https' && $host === 'images.unsplash.com' ? $url : null;
    }

    private function safeEmbedUrl(string $url): ?string
    {
        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https') {
            return null;
        }

        if ($host === 'www.youtube-nocookie.com' && preg_match('~^embed/[A-Za-z0-9_-]+$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'open.spotify.com' && preg_match('~^embed/(track|episode|show|playlist|album)/[A-Za-z0-9]+$~', $path)) {
            return $url;
        }

        if ($host === 'codepen.io' && preg_match('~^[^/]+/embed/[A-Za-z0-9]+$~', $path)) {
            return $url;
        }

        return null;
    }
}
