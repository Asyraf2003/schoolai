<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Admin Arabic Canvas',
        'email' => 'admin-arabic-canvas@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);
});

it('autosaves sanitizes and renders Arabic native article content', function (): void {
    expect(file_get_contents(resource_path('views/layouts/article-canvas.blade.php')))
        ->toContain('article-canvas-arabic.js')
        ->toContain('data-article-canvas-arabic-data');

    expect(file_get_contents(resource_path('js/pages/article-canvas-arabic.js')))
        ->toContain("button.dataset.language = 'ar'")
        ->toContain("documentSection.dataset.documentLanguage = 'ar'");

    $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $this->patchJson(route('admin.artikel.canvas.autosave', $article), [
        'title_id' => 'Generasi Muslim Masa Depan',
        'subtitle_id' => 'Artikel utama berbahasa Indonesia',
        'content_id' => '<p>Konten utama Indonesia.</p>',
        'title_en' => 'The Future Muslim Generation',
        'subtitle_en' => 'English supporting content',
        'content_en' => '<p>English article content.</p>',
        'title_ar' => 'جيل مسلم مستعد للمستقبل',
        'subtitle_ar' => 'مقال عربي من مدرسة المستقبل',
        'content_ar' => '<p class="article-align-right" onclick="alert(1)">محتوى <strong>عربي</strong> آمن.</p><script>alert(2)</script>',
    ])->assertOk()->assertJsonPath('saved_label', 'Draft · Tersimpan');

    $article->refresh();

    expect($article->title_ar)
        ->toBe('جيل مسلم مستعد للمستقبل')
        ->and($article->subtitle_ar)->toBe('مقال عربي من مدرسة المستقبل')
        ->and($article->description_ar)->toContain('محتوى عربي آمن')
        ->and($article->content_ar)->toContain('<strong>عربي</strong>')
        ->and($article->content_ar)->toContain('article-align-right')
        ->and($article->content_ar)->not->toContain('onclick')
        ->and($article->content_ar)->not->toContain('<script');

    $this->postJson(route('admin.artikel.canvas.publish', $article), [
        'publish_mode' => 'now',
        'tags' => ['Islam', 'Sekolah'],
        'published_at' => now()->subMinute()->toIso8601String(),
    ])->assertOk();

    $article->refresh();

    $this->withSession(['locale' => 'ar'])
        ->get(route('artikel.native', ['article' => $article->slug]))
        ->assertOk()
        ->assertSee('جيل مسلم مستعد للمستقبل')
        ->assertSee('مقال عربي من مدرسة المستقبل')
        ->assertSee('محتوى عربي آمن')
        ->assertDontSee('Konten utama Indonesia.');
});

it('falls back from Arabic to available native article content', function (): void {
    $article = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'fallback-arabic-native-article',
        'title_id' => 'Judul Indonesia',
        'subtitle_en' => 'English subtitle fallback',
        'content_en' => '<p>English content fallback.</p>',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => '/artikel/fallback-arabic-native-article',
        'published_at' => now()->subMinute(),
    ]);

    $this->withSession(['locale' => 'ar'])
        ->get(route('artikel.native', ['article' => $article->slug]))
        ->assertOk()
        ->assertSee('Judul Indonesia')
        ->assertSee('English subtitle fallback')
        ->assertSee('English content fallback.');
});
