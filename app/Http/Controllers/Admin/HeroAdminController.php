<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesHeroPlacements;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSetting;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class HeroAdminController extends Controller
{
    use ManagesHeroPlacements;

    public function __construct()
    {
        app()->setLocale('id');
    }

    public function index(): View
    {
        return view('admin.hero.index', [
            'adminPageKey' => 'hero',
            'setting' => HeroSetting::query()->firstOrFail(),
            'promotedArticles' => Article::query()
                ->whereNotNull('hero_position')
                ->orderBy('hero_position')
                ->get(['id', 'title_id', 'title_en', 'title_ar', 'article_source', 'article_status', 'published_at', 'hero_position']),
            'articleOptions' => Article::query()
                ->latestPublished()
                ->whereNull('hero_position')
                ->limit(100)
                ->get(['id', 'title_id', 'title_en', 'title_ar', 'published_at']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'eyebrow_id' => ['nullable', 'string', 'max:160'],
            'eyebrow_en' => ['nullable', 'string', 'max:160'],
            'eyebrow_ar' => ['nullable', 'string', 'max:160'],
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description_id' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'cta_label_id' => ['nullable', 'required_with:cta_url', 'string', 'max:160'],
            'cta_label_en' => ['nullable', 'string', 'max:160'],
            'cta_label_ar' => ['nullable', 'string', 'max:160'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $data = array_map($this->nullableText(...), $data);
        $data['title_id'] = trim((string) $data['title_id']);
        $data['cta_url'] = $this->normalizeLink($data['cta_url'] ?? null);

        if ($data['cta_url'] === null) {
            $data['cta_label_id'] = null;
            $data['cta_label_en'] = null;
            $data['cta_label_ar'] = null;
        }

        HeroSetting::query()->firstOrFail()->update($data);

        return back()->with('success', 'Copy dan CTA Opening Hero berhasil diperbarui.');
    }

    private function normalizeLink(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        $normalized = PublicUrl::normalize($url, ['/admin', '/login', '/auth']);

        if ($normalized === null) {
            throw ValidationException::withMessages([
                'cta_url' => 'Link CTA harus berupa anchor, path internal, atau URL publik yang aman.',
            ]);
        }

        return $normalized;
    }

    private function nullableText(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
