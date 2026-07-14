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
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeAuditGoogleLogin(
    string $googleId,
    string $email,
    string $name = 'Audit Google User'
): void {
    $googleUser = (new GoogleUser())->map([
        'id' => $googleId,
        'email' => $email,
        'name' => $name,
        'nickname' => null,
    ]);

    $googleUser->user = [
        'email_verified' => true,
    ];

    $provider = Mockery::mock();

    $provider->shouldReceive('user')
        ->once()
        ->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);
}

it('records successful and denied admin authentication events', function (): void {
    $activeAdmin = User::query()->forceCreate([
        'name' => 'Audit Active Admin',
        'email' => 'audit-active-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-active-admin-id',
        'role' => User::ROLE_ADMIN,
    ]);

    fakeAuditGoogleLogin(
        $activeAdmin->google_id,
        $activeAdmin->email,
        $activeAdmin->name
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseHas('security_audit_logs', [
        'actor_user_id' => $activeAdmin->id,
        'event' => 'auth.admin.login_succeeded',
    ]);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('security_audit_logs', [
        'actor_user_id' => $activeAdmin->id,
        'event' => 'auth.admin.logout',
    ]);

    $disabledAdmin = User::query()->forceCreate([
        'name' => 'Audit Disabled Admin',
        'email' => 'audit-disabled-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-disabled-admin-id',
        'role' => User::ROLE_ADMIN,
        'disabled_at' => now(),
    ]);

    fakeAuditGoogleLogin(
        $disabledAdmin->google_id,
        $disabledAdmin->email,
        $disabledAdmin->name
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $denied = SecurityAuditLog::query()
        ->where('event', 'auth.admin.login_denied')
        ->where('actor_user_id', $disabledAdmin->id)
        ->firstOrFail();

    expect($denied->metadata)->toBe([
        'reason' => 'account_disabled',
    ]);
});

it('records identity conflicts without storing the email or Google ID', function (): void {
    User::query()->forceCreate([
        'name' => 'Audit Existing Admin',
        'email' => 'audit-conflict@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => null,
        'role' => User::ROLE_ADMIN,
    ]);

    fakeAuditGoogleLogin(
        'different-google-id',
        'audit-conflict@example.test'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $log = SecurityAuditLog::query()
        ->where(
            'event',
            'auth.google.identity_conflict'
        )
        ->firstOrFail();

    $encoded = json_encode($log->metadata);

    expect($encoded)
        ->not->toContain('audit-conflict@example.test')
        ->not->toContain('different-google-id');
});

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

    $article->update([
        'title_id' => 'Artikel audit diperbarui',
    ]);
    $article->delete();
    $article->restore();

    expect(
        SecurityAuditLog::query()
            ->where('actor_user_id', $admin->id)
            ->where(
                'auditable_type',
                $article->getMorphClass()
            )
            ->where(
                'auditable_id',
                (string) $article->id
            )
            ->pluck('event')
            ->all()
    )->toEqualCanonicalizing([
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
        expect(class_uses_recursive($model))
            ->toContain(AuditsAdminChanges::class);
    }
});

it('records account disablement and role changes without sensitive values', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Audit Account Admin',
        'email' => 'audit-account-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $target = User::query()->forceCreate([
        'name' => 'Audit Target User',
        'email' => 'audit-target@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-target-google-id',
        'role' => User::ROLE_USER,
    ]);

    $this->actingAs($admin);

    $target->forceFill([
        'disabled_at' => now(),
        'role' => User::ROLE_ADMIN,
    ])->save();

    $events = SecurityAuditLog::query()
        ->where(
            'auditable_type',
            $target->getMorphClass()
        )
        ->where(
            'auditable_id',
            (string) $target->id
        )
        ->pluck('event');

    expect($events)
        ->toContain('account.disabled')
        ->toContain('account.role_changed');

    $encoded = SecurityAuditLog::query()
        ->where(
            'auditable_id',
            (string) $target->id
        )
        ->get()
        ->toJson();

    expect($encoded)
        ->not->toContain('audit-target@example.test')
        ->not->toContain('audit-target-google-id')
        ->not->toContain('unused-password');
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
        'nested' => [
            'safe' => true,
        ],
    ]);

    expect(fn () => $log->update(['event' => 'changed']))
        ->toThrow(LogicException::class);

    expect(fn () => $log->delete())
        ->toThrow(LogicException::class);
});
