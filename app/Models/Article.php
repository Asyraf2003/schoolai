<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Article extends Model
{
    use \App\Models\Concerns\BuildsArticleQueries;
    use \App\Models\Concerns\DescribesArticleState;
    use \App\Models\Concerns\ResolvesArticlePresentation;

    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent, SoftDeletes;

    public const DEFAULT_AUTHOR = 'Admin';
    public const SOURCE_EXTERNAL = 'external';
    public const SOURCE_NATIVE = 'native';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_SCHEDULED = 'scheduled';
    public const PLACEHOLDER_THUMBNAIL = '/images/article-placeholder.svg';

    protected $fillable = [
        'article_source',
        'article_status',
        'slug',
        'title_id',
        'title_en',
        'title_ar',
        'subtitle_id',
        'subtitle_en',
        'subtitle_ar',
        'description_id',
        'description_en',
        'description_ar',
        'content_id',
        'content_en',
        'content_ar',
        'tags',
        'word_count',
        'thumbnail_url',
        'link_id',
        'link_en',
        'link_ar',
        'author',
        'published_date',
        'published_at',
        'scheduled_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'word_count' => 'integer',
        'published_date' => 'date',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];


































}

