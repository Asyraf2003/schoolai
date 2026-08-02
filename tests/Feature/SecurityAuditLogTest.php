<?php

use App\Models\Article;
use App\Models\Concerns\AuditsAdminChanges;
use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Models\SecurityAuditLog;
use App\Models\SiteStatistic;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('records admin content lifecycle events through one shared concern', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Audit Content Admin',
        'email' => 'audit-content-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);
    $this->actingAs($admin);

    $article = Article::query()->create([
        'title_id' => 'Artikel audit',
        'thumbnail_url' => '/storage/articles/audit.jpg',
        'link_id' => 'https://example.com/audit',
        'author' => 'Audit Admin',
        'published_at' => now(),
    ]);
    $article->update(['title_id' => 'Artikel audit diperbarui']);
    $article->delete();
    $article->restore();

    expect(SecurityAuditLog::query()
        ->where('actor_user_id', $admin->id)
        ->where('auditable_type', $article->getMorphClass())
        ->where('auditable_id', (string) $article->id)
        ->pluck('event')->all())->toEqualCanonicalizing([
            'content.article.created',
            'content.article.updated',
            'content.article.deleted',
            'content.article.restored',
        ]);
});

it('attaches the audit concern to every admin content model', function (): void {
    $models = [
        Article::class,
        GalleryItem::class,
        GalleryPageSection::class,
        GalleryPageMediaItem::class,
        PpdbSetting::class,
        PpdbShowcaseItem::class,
        SiteStatistic::class,
    ];

    foreach ($models as $model) {
        expect(class_uses_recursive($model))->toContain(AuditsAdminChanges::class);
    }
});

it('removes sensitive metadata keys and keeps logs append only', function (): void {
    $log = app(AuditLogger::class)->record(
        'security.metadata_test',
        metadata: [
            'reason' => 'test',
            'access_token' => 'must-not-exist',
            'client_secret' => 'must-not-exist',
            'password' => 'must-not-exist',
            'nested' => [
                'session_payload' => 'must-not-exist',
                'safe' => true,
            ],
        ],
    );

    expect($log->metadata)->toBe([
        'reason' => 'test',
        'nested' => ['safe' => true],
    ]);
    expect(fn () => $log->update(['event' => 'changed']))
        ->toThrow(LogicException::class);
    expect(fn () => $log->delete())->toThrow(LogicException::class);
});
