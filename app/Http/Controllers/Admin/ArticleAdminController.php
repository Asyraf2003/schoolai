<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class ArticleAdminController extends Controller
{
    private const MAX_THUMBNAIL_KB = 10240;

    public function __construct()
    {
        app()->setLocale('id');
    }

    public function index(): View
    {
        $articles = Article::query()
            ->latestPublished()
            ->paginate(20);

        return view('admin.articles.index', [
            'adminPageKey' => 'artikel',
            'articles' => $articles,
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.form', [
            'adminPageKey' => 'artikel',
            'mode' => 'create',
            'article' => new Article([
                'author' => Article::DEFAULT_AUTHOR,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->applyThumbnail($request, $data);

        $article = Article::query()->create($data);

        return redirect()
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function show(Article $article): View
    {
        return view('admin.articles.show', [
            'adminPageKey' => 'artikel',
            'article' => $article,
        ]);
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', [
            'adminPageKey' => 'artikel',
            'mode' => 'edit',
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validatedData($request, $article);
        $data = $this->applyThumbnail($request, $data, $article);

        $article->update($data);

        return redirect()
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->deleteStoredPublicFile($article->thumbnail_url);
        $article->delete();

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Article $article = null): array
    {
        $needsThumbnailFile = ! $article?->exists || ! $article->thumbnail_url;

        $validator = validator($request->all(), [
            'title_id' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'description_id' => ['nullable', 'string', 'max:600'],
            'description_en' => ['nullable', 'string', 'max:600'],
            'thumbnail_file' => [
                Rule::requiredIf(fn (): bool => $needsThumbnailFile),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_THUMBNAIL_KB,
            ],
            'link_id' => ['required', 'url', 'max:2048'],
            'link_en' => ['nullable', 'url', 'max:2048'],
            'author' => ['nullable', 'string', 'max:120'],
            'published_at' => ['nullable', 'date'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'thumbnail_file.required' => 'Thumbnail wajib diupload.',
            'thumbnail_file.file' => 'Thumbnail harus berupa file.',
            'thumbnail_file.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail_file.mimes' => 'Thumbnail harus JPG, PNG, atau WebP.',
            'thumbnail_file.max' => 'Ukuran thumbnail maksimal 10MB.',
            'link_id.required' => 'Link artikel Indonesia wajib diisi.',
            'link_id.url' => 'Link artikel Indonesia tidak valid.',
            'link_en.url' => 'Link artikel English tidak valid.',
            'published_at.date' => 'Tanggal dan waktu publikasi tidak valid.',
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            foreach (['link_id' => 'Link artikel Indonesia', 'link_en' => 'Link artikel English'] as $field => $label) {
                $url = $this->nullableText($request->input($field));

                if ($url && ! $this->isPublicArticleUrl($url)) {
                    $validator->errors()->add($field, $label . ' harus memakai URL publik, bukan localhost, IP lokal, login, atau halaman admin.');
                }
            }
        });

        $data = $validator->validate();

        unset($data['thumbnail_file']);

        $data['title_en'] = $this->nullableText($data['title_en'] ?? null);
        $data['description_id'] = $this->nullableText($data['description_id'] ?? null);
        $data['description_en'] = $this->nullableText($data['description_en'] ?? null);
        $data['link_en'] = $this->nullableText($data['link_en'] ?? null);
        $data['author'] = $this->nullableText($data['author'] ?? null) ?: Article::DEFAULT_AUTHOR;
        $data['published_at'] = $data['published_at']
            ?? optional($article?->published_at)->format('Y-m-d H:i:s')
            ?? now()->format('Y-m-d H:i:s');

        return $data;
    }

    private function applyThumbnail(Request $request, array $data, ?Article $currentArticle = null): array
    {
        if (! $request->hasFile('thumbnail_file')) {
            return $data;
        }

        $this->deleteStoredPublicFile($currentArticle?->thumbnail_url);

        $data['thumbnail_url'] = Storage::url(
            $request->file('thumbnail_file')->store('articles/thumbnails', 'public')
        );

        return $data;
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function isPublicArticleUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            $host === '::1' ||
            str_ends_with($host, '.local') ||
            str_starts_with($host, '10.') ||
            str_starts_with($host, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $host) === 1
        ) {
            return false;
        }

        return ! (
            $path === '/admin' ||
            str_starts_with($path, '/admin/') ||
            $path === '/login' ||
            str_starts_with($path, '/auth/')
        );
    }

    private function nullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
