<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ArticleAdminController extends Controller
{
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
        $article = Article::query()->create($this->validatedData($request));

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
        $article->update($this->validatedData($request, $article));

        return redirect()
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Article $article = null): array
    {
        $data = $request->validate([
            'title_id' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'thumbnail_url' => ['required', 'url', 'max:2048'],
            'link_id' => ['required', 'url', 'max:2048'],
            'link_en' => ['nullable', 'url', 'max:2048'],
            'author' => ['nullable', 'string', 'max:120'],
            'published_date' => ['nullable', 'date'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'thumbnail_url.required' => 'Thumbnail wajib diisi.',
            'thumbnail_url.url' => 'URL thumbnail tidak valid.',
            'link_id.required' => 'Link artikel Indonesia wajib diisi.',
            'link_id.url' => 'Link artikel Indonesia tidak valid.',
            'link_en.url' => 'Link artikel English tidak valid.',
            'published_date.date' => 'Tanggal publikasi tidak valid.',
        ]);

        $data['title_en'] = $this->nullableText($data['title_en'] ?? null);
        $data['link_en'] = $this->nullableText($data['link_en'] ?? null);
        $data['author'] = $this->nullableText($data['author'] ?? null) ?: Article::DEFAULT_AUTHOR;
        $data['published_date'] = $data['published_date'] ?? optional($article?->published_date)->toDateString() ?? today()->toDateString();

        return $data;
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
