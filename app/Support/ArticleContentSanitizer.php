<?php

namespace App\Support;

use App\Support\Concerns\SanitizesArticleContent;
use App\Support\Concerns\SanitizesArticleDom;
use App\Support\Concerns\ValidatesArticleContentUrls;

final class ArticleContentSanitizer
{
    use SanitizesArticleContent;
    use SanitizesArticleDom;
    use ValidatesArticleContentUrls;

    private const MAX_BYTES = 2_000_000;

    private const ALLOWED_TAGS = [
        'a', 'blockquote', 'br', 'code', 'div', 'em', 'figcaption', 'figure',
        'h2', 'h3', 'hr', 'iframe', 'img', 'li', 'mark', 'ol', 'p', 'pre', 's',
        'span', 'strike', 'strong', 'ul',
    ];

    private const DROP_WITH_CONTENT = [
        'applet', 'audio', 'canvas', 'embed', 'form', 'input', 'link', 'math',
        'meta', 'object', 'script', 'style', 'svg', 'template', 'textarea', 'video',
    ];
}
