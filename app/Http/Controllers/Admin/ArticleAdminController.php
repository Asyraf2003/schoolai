<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
                'published_date' => today(),
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

        $data = $request->validate([
            'title_id' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
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
            'published_date' => ['nullable', 'date'],
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
            'published_date.date' => 'Tanggal publikasi tidak valid.',
        ]);

        unset($data['thumbnail_file']);

        $data['title_en'] = $this->nullableText($data['title_en'] ?? null);
        $data['link_en'] = $this->nullableText($data['link_en'] ?? null);
        $data['author'] = $this->nullableText($data['author'] ?? null) ?: Article::DEFAULT_AUTHOR;
        $data['published_date'] = $data['published_date'] ?? optional($article?->published_date)->toDateString() ?? today()->toDateString();

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

    private function nullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
