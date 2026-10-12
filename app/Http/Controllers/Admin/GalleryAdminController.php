<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Rules\SafeImageUpload;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

final class GalleryAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.gallery.index', ['items' => GalleryItem::withTrashed()->orderByDesc('id')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.gallery.form', ['item' => new GalleryItem]);
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.form', ['item' => $galleryItem]);
    }

    public function store(Request $request, R2MediaStorage $storage): RedirectResponse
    {
        return $this->save($request, new GalleryItem, $storage);
    }

    public function update(Request $request, GalleryItem $galleryItem, R2MediaStorage $storage): RedirectResponse
    {
        return $this->save($request, $galleryItem, $storage);
    }

    private function save(Request $request, GalleryItem $item, R2MediaStorage $storage): RedirectResponse
    {
        $data = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:300'],
            'media_file' => [$item->exists ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:102400', Rule::when(str_starts_with($request->file('media_file')?->getMimeType() ?? '', 'image/'), new SafeImageUpload)],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240', new SafeImageUpload],
            'remove_thumbnail' => ['sometimes', 'boolean'],
        ]);
        $uploads = [];
        unset($data['media_file'], $data['thumbnail_file'], $data['remove_thumbnail']);
        if ($request->boolean('remove_thumbnail')) {
            $data['thumbnail_url'] = null;
        }
        try {
            if ($request->hasFile('media_file')) {
                $file = $request->file('media_file');
                $upload = $storage->store($file, 'gallery/media', $item->id);
                $uploads[] = $upload;
                $data['media_url'] = $upload['url'];
                $data['type'] = str_starts_with($file->getMimeType(), 'image/') ? 'photo' : 'video';
                $data['thumbnail_url'] = null;
            }
            if ($request->hasFile('thumbnail_file')) {
                $upload = $storage->store($request->file('thumbnail_file'), 'gallery/thumbnails', $item->id);
                $uploads[] = $upload;
                $data['thumbnail_url'] = $upload['url'];
            }
            DB::transaction(fn () => $item->fill($data)->save());
        } catch (Throwable $exception) {
            foreach ($uploads as $upload) {
                $storage->deleteKey($upload['key']);
            }
            throw $exception;
        }

        return redirect()->route('admin.galeri')->with('success', 'Media berhasil disimpan.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->delete();

        return redirect()->route('admin.galeri')->with('success', 'Media berhasil dihapus.');
    }

    public function restore(GalleryItem $galleryItem): RedirectResponse
    {
        abort_unless($galleryItem->trashed(), 404);
        $galleryItem->restore();

        return redirect()->route('admin.galeri')->with('success', 'Media berhasil dipulihkan.');
    }
}
