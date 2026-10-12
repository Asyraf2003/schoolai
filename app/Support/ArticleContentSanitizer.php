<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

final class ArticleContentSanitizer
{
    private const ALLOWED = ['a', 'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h2', 'h3', 'blockquote', 'ul', 'ol', 'li', 'code', 'pre', 'hr'];

    private const DROP = ['script', 'style', 'img', 'iframe', 'video', 'audio', 'object', 'embed', 'svg', 'canvas', 'template', 'form', 'input', 'textarea', 'math'];

    public function sanitize(?string $html): string
    {
        if (trim((string) $html) === '') {
            return '';
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.substr($html, 0, 2_000_000).'</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }
        $this->clean($body);
        $result = '';
        foreach ($body->childNodes as $node) {
            $result .= $dom->saveHTML($node);
        }

        return trim($result);
    }

    private function clean(DOMNode $parent): void
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
            if (in_array($tag, self::DROP, true)) {
                $parent->removeChild($node);

                continue;
            }
            $href = $tag === 'a' ? PublicUrl::normalize($node->getAttribute('href'), ['/admin', '/login', '/auth']) : null;
            foreach (iterator_to_array($node->attributes) as $attribute) {
                $node->removeAttribute($attribute->name);
            }
            if ($href !== null) {
                $node->setAttribute('href', $href);
                $node->setAttribute('rel', 'noopener noreferrer');
            }
            $this->clean($node);
            if (! in_array($tag, self::ALLOWED, true)) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
            }
        }
    }
}
