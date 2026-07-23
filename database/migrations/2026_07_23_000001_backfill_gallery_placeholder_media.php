<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fallbackImages = [
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1600&q=82',
        ];

        $this->backfillTable('gallery_items', $fallbackImages);
        $this->backfillTable('gallery_page_media_items', $fallbackImages);
    }

    public function down(): void
    {
        // The previous values were missing, so there is nothing meaningful to restore.
    }

    /** @param array<int, string> $fallbackImages */
    private function backfillTable(string $table, array $fallbackImages): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'media_url')) {
            return;
        }

        $rows = DB::table($table)
            ->where(function ($query): void {
                $query
                    ->whereNull('media_url')
                    ->orWhere('media_url', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($rows as $index => $row) {
            $updates = [
                'media_url' => $fallbackImages[$index % count($fallbackImages)],
            ];

            if (Schema::hasColumn($table, 'type')) {
                $updates['type'] = 'photo';
            }

            DB::table($table)
                ->where('id', $row->id)
                ->update($updates);
        }
    }
};
