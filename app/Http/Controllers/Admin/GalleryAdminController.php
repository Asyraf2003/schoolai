<?php
/* ADMIN_GALLERY_DUMMY_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class GalleryAdminController extends Controller
{
    private const MAX_ITEMS = 6;
    private const MAX_VIDEO_SECONDS = 180;

    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        $items = $this->galleryItems();

        return view('admin.gallery.index', [
            'adminPageKey' => 'galeri',
            'items' => $items,
            'limits' => [
                'max_items' => self::MAX_ITEMS,
                'min_items' => 1,
                'max_video_seconds' => self::MAX_VIDEO_SECONDS,
                'max_video_minutes' => 3,
            ],
            'dbMap' => $this->databaseMap(),
        ]);
    }

    private function galleryItems(): array
    {
        $items = __('home.galeri.items');

        if (! is_array($items)) {
            return [];
        }

        $items = array_values(array_filter(
            array_map(fn (mixed $item): ?array => is_array($item) ? $this->normalizeItem($item) : null, $items)
        ));

        usort(
            $items,
            fn (array $first, array $second): int => strcmp(
                (string) ($second['published_at'] ?? ''),
                (string) ($first['published_at'] ?? '')
            )
        );

        return array_slice($items, 0, self::MAX_ITEMS);
    }

    private function normalizeItem(array $item): array
    {
        $type = $item['type'] ?? 'photo';

        if (! in_array($type, ['photo', 'video', 'reel'], true)) {
            $type = 'photo';
        }

        $durationSeconds = (int) ($item['duration_seconds'] ?? 0);

        if ($type === 'photo') {
            $durationSeconds = 0;
        }

        if ($type !== 'photo' && $durationSeconds <= 0) {
            $durationSeconds = 60;
        }

        $durationSeconds = min($durationSeconds, self::MAX_VIDEO_SECONDS);

        return [
            'title' => (string) ($item['title'] ?? 'Galeri tanpa judul'),
            'type' => $type,
            'type_label' => match ($type) {
                'video' => 'Video',
                'reel' => 'Reel',
                default => 'Foto',
            },
            'is_video' => $type !== 'photo',
            'duration_seconds' => $durationSeconds,
            'duration_label' => $durationSeconds > 0 ? $this->durationLabel($durationSeconds) : '-',
            'caption' => (string) ($item['caption'] ?? ''),
            'category' => (string) ($item['category'] ?? 'Umum'),
            'published_at' => (string) ($item['published_at'] ?? $item['date'] ?? ''),
            'date' => (string) ($item['date'] ?? $item['published_at'] ?? ''),
            'thumbnail' => (string) ($item['thumbnail'] ?? ''),
            'video_path' => (string) ($item['video_path'] ?? ''),
            'instagram_url' => (string) ($item['instagram_url'] ?? ''),
            'fallback_icon' => (string) ($item['fallback_icon'] ?? $item['emoji'] ?? '📸'),
            'accent' => (string) ($item['accent'] ?? '#19aee6'),
        ];
    }

    private function durationLabel(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    private function databaseMap(): array
    {
        return [
            ['field' => 'id', 'type' => 'bigint unsigned', 'note' => 'Primary key.'],
            ['field' => 'title', 'type' => 'varchar(160)', 'note' => 'Judul item galeri. Wajib.'],
            ['field' => 'type', 'type' => 'enum/photo,video,reel', 'note' => 'Jenis konten. Video dan reel dibatasi 3 menit.'],
            ['field' => 'category', 'type' => 'varchar(80)', 'note' => 'Kategori tampilan seperti Kegiatan, Tahfidz, Bahasa.'],
            ['field' => 'caption', 'type' => 'text nullable', 'note' => 'Deskripsi singkat untuk publik.'],
            ['field' => 'thumbnail_path', 'type' => 'varchar(255) nullable', 'note' => 'Path gambar thumbnail lokal.'],
            ['field' => 'media_path', 'type' => 'varchar(255) nullable', 'note' => 'Path foto/video lokal jika nanti upload aktif.'],
            ['field' => 'duration_seconds', 'type' => 'unsigned smallint nullable', 'note' => 'Wajib untuk video/reel. Max 180.'],
            ['field' => 'sort_order', 'type' => 'unsigned tinyint', 'note' => 'Urutan tampil. Max 6 item aktif.'],
            ['field' => 'is_published', 'type' => 'boolean', 'note' => 'Status tampil di publik.'],
            ['field' => 'published_at', 'type' => 'timestamp nullable', 'note' => 'Tanggal publikasi.'],
        ];
    }
}
