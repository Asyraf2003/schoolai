<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\ArticleContentSanitizer;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

final class ArticleAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.articles.index', [
            'articles' => Article::withTrashed()->orderByDesc('id')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.form', ['article' => new Article]);
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', compact('article'));
    }

    public function store(Request $request, R2MediaStorage $storage): RedirectResponse
    {
        return $this->save($request, new Article, $storage);
    }

    public function update(Request $request, Article $article, R2MediaStorage $storage): RedirectResponse
    {
        return $this->save($request, $article, $storage);
    }

    private function save(Request $request, Article $article, R2MediaStorage $storage): RedirectResponse
    {
        $rules = ['published_date' => ['nullable', 'date_format:Y-m-d'], 'remove_cover' => ['sometimes', 'boolean'],
            'remove_cover_thumbnail' => ['sometimes', 'boolean'],
            'cover_thumbnail_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240', new SafeImageUpload],
            'cover_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:102400', Rule::when(str_starts_with($request->file('cover_file')?->getMimeType() ?? '', 'image/'), new SafeImageUpload)]];
        foreach (['id', 'en', 'ar'] as $locale) {
            $rules['title_'.$locale] = ['nullable', 'string', 'max:200'];
            $rules['description_'.$locale] = ['nullable', 'string', 'max:2000'];
            $rules['content_'.$locale] = ['nullable', 'string', 'max:2000000'];
        }
        $data = $request->validate($rules);
        $sanitizer = app(ArticleContentSanitizer::class);
        $hasText = false;
        foreach (['id', 'en', 'ar'] as $locale) {
            $content = $sanitizer->sanitize($data['content_'.$locale] ?? null);
            $data['content_'.$locale] = $content ?: null;
            $hasText = $hasText || trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8')) !== '';
        }
        if (! $hasText) {
            throw ValidationException::withMessages(['content_id' => 'Isi artikel wajib berupa teks pada minimal satu bahasa.']);
        }
        unset($data['cover_file'], $data['remove_cover'], $data['cover_thumbnail_file'], $data['remove_cover_thumbnail']);
        if ($request->boolean('remove_cover')) {
            $data['cover_url'] = null;
            $data['cover_thumbnail_url'] = null;
        }
        if ($request->boolean('remove_cover_thumbnail')) {
            $data['cover_thumbnail_url'] = null;
        }
        $uploads = [];
        try {
            if ($request->hasFile('cover_file')) {
                $upload = $storage->store($request->file('cover_file'), 'articles/covers', $article->id);
                $uploads[] = $upload;
                $data['cover_url'] = $upload['url'];
                $data['cover_thumbnail_url'] = null;
            }
            if ($request->hasFile('cover_thumbnail_file')) {
                $upload = $storage->store($request->file('cover_thumbnail_file'), 'articles/thumbnails', $article->id);
                $uploads[] = $upload;
                $data['cover_thumbnail_url'] = $upload['url'];
            }
            DB::transaction(fn () => $article->fill($data)->save());
        } catch (Throwable $exception) {
            foreach ($uploads as $upload) {
                $storage->deleteKey($upload['key']);
            }
            throw $exception;
        }

        return redirect()->route('admin.artikel')->with('success', 'Artikel berhasil disimpan.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.artikel')->with('success', 'Artikel berhasil dihapus.');
    }

    public function restore(Article $article): RedirectResponse
    {
        abort_unless($article->trashed(), 404);
        $article->restore();

        return redirect()->route('admin.artikel')->with('success', 'Artikel berhasil dipulihkan.');
    }
}
