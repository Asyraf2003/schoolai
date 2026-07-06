<?php
/* GALLERY_PAGE_SECTIONS_MEDIA_MIGRATION_FINAL */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_page_sections', function (Blueprint $table): void {
            $table->id();
            $table->text('title_id');
            $table->text('title_en')->nullable();
            $table->longText('description_id')->nullable();
            $table->longText('description_en')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('gallery_page_media_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gallery_page_section_id')->constrained('gallery_page_sections')->cascadeOnDelete();
            $table->text('title_id');
            $table->text('title_en')->nullable();
            $table->longText('description_id')->nullable();
            $table->longText('description_en')->nullable();
            $table->string('type', 16)->default('photo');
            $table->string('media_url', 2048)->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['gallery_page_section_id', 'is_published'], 'gallery_page_media_section_active_idx');
        });

        $now = now();

        DB::table('gallery_page_sections')->insert([
            ['title_id' => 'Info Terbaru', 'title_en' => 'Latest Updates', 'description_id' => 'Kabar singkat, agenda, dan informasi ringan dari sekolah.', 'description_en' => 'Short updates, agenda notes, and light school information.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Program', 'title_en' => 'Programs', 'description_id' => 'Dokumentasi program belajar dan pembiasaan anak.', 'description_en' => 'Documentation of learning programs and student routines.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Prestasi', 'title_en' => 'Achievements', 'description_id' => 'Pencapaian akademik, karakter, kreativitas, dan hafalan.', 'description_en' => 'Academic, character, creative, and memorization achievements.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Fasilitas', 'title_en' => 'Facilities', 'description_id' => 'Ruang kelas, area bermain, pojok baca, dan fasilitas pendukung.', 'description_en' => 'Classrooms, play areas, reading corners, and supporting facilities.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Testimoni', 'title_en' => 'Testimonials', 'description_id' => 'Cerita singkat dari wali murid dan keluarga sekolah.', 'description_en' => 'Short stories from parents and the school community.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Laporan', 'title_en' => 'Reports', 'description_id' => 'Ringkasan kegiatan dan dokumentasi publik sekolah.', 'description_en' => 'Activity summaries and public school documentation.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title_id' => 'Media', 'title_en' => 'Media', 'description_id' => 'Slot foto, video, dan embed sosial pilihan.', 'description_en' => 'Selected photo, video, and social embed slots.', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_page_media_items');
        Schema::dropIfExists('gallery_page_sections');
    }
};
