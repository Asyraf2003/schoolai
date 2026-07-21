<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Throwable;

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
            ->withTrashed()
            ->latestPublished()
            ->paginate(20);

        $activeArticlesByIdentity = Article::query()
            ->latestPublished()
            ->get(['id', 'title_id', 'title_en', 'link_id', 'published_at'])
            ->groupBy(function (Article $article): string {
                return Article::normalizedLinkIdentity($article->link_id)
                    ?? '__invalid_active_' . $article->getKey();
            });

        $replacementCandidatesByArticle = $articles
            ->getCollection()
            ->filter(fn (Article $article): bool => $article->trashed())
            ->mapWithKeys(function (Article $article) use ($activeArticlesByIdentity): array {
                $identity = Article::normalizedLinkIdentity($article->link_id);

                return [
                    $article->getKey() => $identity
                        ? $activeArticlesByIdentity->get($identity, collect())
                        : collect(),
                ];
            });

        return view('admin.articles.index', [
            'adminPageKey' => 'artikel',
            'articles' => $articles,
            'replacementCandidatesByArticle' => $replacementCandidatesByArticle,
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
        [$data, $newPath] = $this->applyThumbnail($request, $data);

        try {
            $article = Article::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

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
        $oldThumbnailUrl = $article->thumbnail_url;
        $data = $this->validatedData($request, $article);
        [$data, $newPath] = $this->applyThumbnail($request, $data);

        try {
            $article->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($newPath !== null) {
            $this->deleteStoredPublicFile($oldThumbnailUrl);
        }

        return redirect()
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $article): RedirectResponse
    {
        $data = $request->validate([
            'replacement_article_id' => ['nullable', 'integer'],
        ]);

        $replacementArticleId = isset($data['replacement_article_id'])
            ? (int) $data['replacement_article_id']
            : null;

        if ($replacementArticleId === null) {
            Article::onlyTrashed()->findOrFail($article)->restore();

            return redirect()
                ->route('admin.artikel')
                ->with('success', 'Artikel berhasil dipulihkan.');
        }

        DB::transaction(function () use ($article, $replacementArticleId): void {
            $trashedArticle = Article::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($article);

            $replacementArticle = Article::query()
                ->lockForUpdate()
                ->findOrFail($replacementArticleId);

            $trashedIdentity = Article::normalizedLinkIdentity($trashedArticle->link_id);
            $replacementIdentity = Article::normalizedLinkIdentity($replacementArticle->link_id);

            if ($trashedIdentity === null || $trashedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_article_id' => 'Artikel pengganti harus merupakan artikel aktif dengan link Indonesia yang identik.',
                ]);
            }

            $replacementArticle->delete();
            $trashedArticle->restore();
        });

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel lama dipulihkan dan artikel aktif pengganti dipindahkan ke arsip.');
    }

    private function validatedData(Request $request, ?Article $article = null): array
    {
        $needsThumbnailFile = ! $article?->exists || ! $article->thumbnail_url;

        $validator = validator($request->all(), [
            'title_id' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'title_ar' => ['nullable', 'string', 'max:200'],
            'description_id' => ['nullable', 'string', 'max:600'],
            'description_en' => ['nullable', 'string', 'max:600'],
            'description_ar' => ['nullable', 'string', 'max:600'],
            'thumbnail_file' => [
                Rule::requiredIf(fn (): bool => $needsThumbnailFile),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:' . self::MAX_THUMBNAIL_KB,
            ],
            'link_id' => ['required', 'url', 'max:2048'],
            'link_en' => ['nullable', 'url', 'max:2048'],
            'link_ar' => ['nullable', 'url', 'max:2048'],
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
            'link_ar.url' => 'Link artikel Arabic tidak valid.',
            'published_at.date' => 'Tanggal dan waktu publikasi tidak valid.',
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            foreach ([
                'link_id' => 'Link artikel Indonesia',
                'link_en' => 'Link artikel English',
                'link_ar' => 'Link artikel Arabic',
            ] as $field => $label) {
                $url = $this->nullableText($request->input($field));

                if ($url && ! $this->isPublicArticleUrl($url)) {
                    $validator->errors()->add($field, $label . ' harus memakai URL publik, bukan localhost, IP lokal, login, atau halaman admin.');
                }
            }
        });

        $data = $validator->validate();

        unset($data['thumbnail_file']);

        $data['title_en'] = $this->nullableText($data['title_en'] ?? null);
        $data['title_ar'] = $this->nullableText($data['title_ar'] ?? null);
        $data['description_id'] = $this->nullableText($data['description_id'] ?? null);
        $data['description_en'] = $this->nullableText($data['description_en'] ?? null);
        $data['description_ar'] = $this->nullableText($data['description_ar'] ?? null);
        $data['link_en'] = $this->nullableText($data['link_en'] ?? null);
        $data['link_ar'] = $this->nullableText($data['link_ar'] ?? null);
        $data['author'] = $this->nullableText($data['author'] ?? null) ?: Article::DEFAULT_AUTHOR;
        $data['published_at'] = $data['published_at']
            ?? optional($article?->published_at)->format('Y-m-d H:i:s')
            ?? now()->format('Y-m-d H:i:s');

        return $data;
    }

    private function applyThumbnail(Request $request, array $data): array
    {
        if (! $request->hasFile('thumbnail_file')) {
            return [$data, null];
        }

        $path = $request->file('thumbnail_file')->store('articles/thumbnails', 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'thumbnail_file' => 'Thumbnail gagal disimpan. Silakan coba lagi.',
            ]);
        }

        $data['thumbnail_url'] = Storage::url($path);

        return [$data, $path];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        if (Article::withTrashed()->where('thumbnail_url', $url)->exists()) {
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
        return PublicUrl::isSafe($url, ['/admin', '/login', '/auth']);
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
