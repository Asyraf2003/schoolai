<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Rules\SafeImageUpload;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesHeroSlides
{
    public function index(): View
    {
        $this->normalizeSortOrders();

        return view('admin.hero.index', [
            'adminPageKey' => 'hero',
            'slides' => HeroSlide::query()->with('article')->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.hero.form', [
            'adminPageKey' => 'hero',
            'mode' => 'create',
            'slide' => new HeroSlide([
                'type' => 'image',
                'focal_position' => 'center center',
                'overlay_strength' => 0.46,
                'sort_order' => $this->nextSortOrder(),
                'is_active' => true,
                'cta_action' => 'anchor',
            ]),
            'articles' => $this->articleOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        [$data, $storedPaths] = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder();

        try {
            HeroSlide::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPaths($storedPaths);
            throw $exception;
        }

        $this->normalizeSortOrders();

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil ditambahkan.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero.form', [
            'adminPageKey' => 'hero',
            'mode' => 'edit',
            'slide' => $heroSlide,
            'articles' => $this->articleOptions(),
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $oldMediaUrl = $heroSlide->media_url;
        $oldPosterUrl = $heroSlide->poster_url;
        $data = $this->validatedData($request, $heroSlide);
        [$data, $storedPaths] = $this->applyMedia($request, $data, $heroSlide);

        try {
            $heroSlide->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPaths($storedPaths);
            throw $exception;
        }

        if (($storedPaths['media'] ?? null) !== null) {
            $this->deleteStoredFile($oldMediaUrl);
        }

        if (($storedPaths['poster'] ?? null) !== null) {
            $this->deleteStoredFile($oldPosterUrl);
        }

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil diperbarui.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $mediaUrl = $heroSlide->media_url;
        $posterUrl = $heroSlide->poster_url;
        $heroSlide->delete();
        $this->deleteStoredFile($mediaUrl);
        $this->deleteStoredFile($posterUrl);
        $this->normalizeSortOrders();

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil dihapus.');
    }

    public function toggle(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->update(['is_active' => ! $heroSlide->is_active]);

        return back()->with('success', 'Status hero slide berhasil diubah.');
    }

    public function moveUp(HeroSlide $heroSlide): RedirectResponse
    {
        $this->move($heroSlide, true);

        return back()->with('success', 'Urutan hero berhasil diperbarui.');
    }

    public function moveDown(HeroSlide $heroSlide): RedirectResponse
    {
        $this->move($heroSlide, false);

        return back()->with('success', 'Urutan hero berhasil diperbarui.');
    }
}
