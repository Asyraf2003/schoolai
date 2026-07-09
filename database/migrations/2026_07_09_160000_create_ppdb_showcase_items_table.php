<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_showcase_items', function (Blueprint $table): void {
            $table->id();
            $table->string('audience', 20)->index();
            $table->string('title_id', 180);
            $table->string('title_en', 180)->nullable();
            $table->text('description_id');
            $table->text('description_en')->nullable();
            $table->string('media_type', 20)->default('photo');
            $table->string('media_url', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['audience', 'sort_order']);
        });

        $now = now();

        DB::table('ppdb_showcase_items')->insert([
            [
                'audience' => 'parents',
                'title_id' => 'Konsultasi Keluarga',
                'title_en' => 'Family Consultation',
                'description_id' => 'Orang tua bertanya kebutuhan anak, jenjang yang cocok, dan jadwal kunjungan sekolah.',
                'description_en' => 'Parents discuss the child’s needs, suitable level, and school visit schedule.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'audience' => 'parents',
                'title_id' => 'Observasi Anak',
                'title_en' => 'Child Observation',
                'description_id' => 'Guru mengenal kesiapan anak lewat bermain, bercerita, dan interaksi ringan.',
                'description_en' => 'Teachers observe children through play, stories, and simple interaction.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'audience' => 'parents',
                'title_id' => 'Orientasi Sekolah',
                'title_en' => 'School Orientation',
                'description_id' => 'Anak mulai beradaptasi dengan kelas, guru, teman, doa harian, dan ritme belajar.',
                'description_en' => 'Children begin adapting to class, teachers, friends, daily prayers, and learning rhythm.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'audience' => 'school',
                'title_id' => 'Siapkan Link Pendaftaran',
                'title_en' => 'Prepare the Registration Link',
                'description_id' => 'Admin sekolah memasang link formulir, brosur, atau panduan yang akan dibuka orang tua.',
                'description_en' => 'School admins prepare the form, brochure, or guide link that parents will open.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'audience' => 'school',
                'title_id' => 'Bagikan Informasi PPDB',
                'title_en' => 'Share Admission Information',
                'description_id' => 'Informasi PPDB bisa dibagikan secara rapi dari halaman publik tanpa mengubah halaman lain.',
                'description_en' => 'Admission information can be shared neatly from the public page without touching other pages.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'audience' => 'school',
                'title_id' => 'Pantau Calon Murid',
                'title_en' => 'Track Prospective Students',
                'description_id' => 'Tim sekolah menjaga alur komunikasi agar proses pendaftaran tetap jelas dan mudah diikuti.',
                'description_en' => 'The school team keeps communication clear so the admission process is easy to follow.',
                'media_type' => 'photo',
                'media_url' => null,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_showcase_items');
    }
};
