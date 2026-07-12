<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->timestamp('published_at')->nullable()->after('published_date');
            $table->index(['published_at', 'id']);
        });

        DB::table('articles')
            ->select(['id', 'published_date'])
            ->whereNull('published_at')
            ->orderBy('id')
            ->chunkById(200, function ($articles): void {
                foreach ($articles as $article) {
                    $publishedAt = Carbon::parse(
                        (string) $article->published_date,
                        config('app.timezone')
                    )->startOfDay();

                    DB::table('articles')
                        ->where('id', $article->id)
                        ->update(['published_at' => $publishedAt]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex(['published_at', 'id']);
            $table->dropColumn('published_at');
        });
    }
};
