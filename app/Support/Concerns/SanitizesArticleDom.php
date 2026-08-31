<?php

namespace App\Support\Concerns;

use DOMElement;
use DOMNode;

trait SanitizesArticleDom
{
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
}
