<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->string('article_source', 20)->default('external')->after('id');
            $table->string('article_status', 20)->default('published')->after('article_source');
            $table->string('slug', 220)->nullable()->unique()->after('article_status');
            $table->string('subtitle_id', 300)->nullable()->after('title_en');
            $table->string('subtitle_en', 300)->nullable()->after('subtitle_id');
            $table->longText('content_id')->nullable()->after('description_en');
            $table->longText('content_en')->nullable()->after('content_id');
            $table->json('tags')->nullable()->after('content_en');
            $table->unsignedInteger('word_count')->default(0)->after('tags');
            $table->timestamp('scheduled_at')->nullable()->after('published_at');

            $table->index(['article_source', 'article_status', 'published_at'], 'articles_publication_index');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex('articles_publication_index');
            $table->dropIndex(['scheduled_at']);
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'article_source',
                'article_status',
                'slug',
                'subtitle_id',
                'subtitle_en',
                'content_id',
                'content_en',
                'tags',
                'word_count',
                'scheduled_at',
            ]);
        });
    }
};
