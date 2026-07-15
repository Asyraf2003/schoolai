<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

final class SafeImageUpload implements ValidationRule
{
    private const MAX_WIDTH = 8192;
    private const MAX_HEIGHT = 8192;
    private const MAX_PIXELS = 40_000_000;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('File gambar gagal diupload dengan benar.');

            return;
        }

        $path = $value->getRealPath();
        $size = $value->getSize();

        if (! is_string($path) || $path === '' || ! is_int($size) || $size < 12) {
            $fail('File gambar kosong atau rusak.');

            return;
        }

        $image = @getimagesize($path);

        if (! is_array($image) || ! isset($image[0], $image[1], $image[2])) {
            $fail('Isi file bukan gambar raster yang valid.');

            return;
        }

        $width = (int) $image[0];
        $height = (int) $image[1];
        $type = (int) $image[2];
        $allowed = [
            IMAGETYPE_JPEG => ['mime' => 'image/jpeg', 'extensions' => ['jpg', 'jpeg']],
            IMAGETYPE_PNG => ['mime' => 'image/png', 'extensions' => ['png']],
            IMAGETYPE_WEBP => ['mime' => 'image/webp', 'extensions' => ['webp']],
        ];

        if (! isset($allowed[$type])) {
            $fail('Format gambar harus JPG, PNG, atau WebP.');

            return;
        }

        if (
            $width < 1 ||
            $height < 1 ||
            $width > self::MAX_WIDTH ||
            $height > self::MAX_HEIGHT ||
            ($width * $height) > self::MAX_PIXELS
        ) {
            $fail('Dimensi gambar terlalu besar. Maksimal 8192×8192 dan 40 megapiksel.');

            return;
        }

        $mime = (string) $value->getMimeType();
        $extension = strtolower($value->getClientOriginalExtension());

        if ($mime !== $allowed[$type]['mime'] || ! in_array($extension, $allowed[$type]['extensions'], true)) {
            $fail('Ekstensi atau MIME file tidak sesuai dengan isi gambar.');

            return;
        }

        $contents = @file_get_contents($path);

        if (! is_string($contents) || strlen($contents) !== $size || ! $this->hasCleanEnding($contents, $type)) {
            $fail('File gambar rusak atau memiliki data tambahan yang tidak diizinkan.');

            return;
        }

        $lower = strtolower($contents);

        if (str_contains($lower, '<?php') || str_contains($lower, '<script') || str_contains($lower, 'javascript:')) {
            $fail('File gambar mengandung payload aktif yang tidak diizinkan.');

            return;
        }

        if ($type === IMAGETYPE_JPEG && preg_match('/\xFF\xE1..Exif\x00\x00/s', $contents) === 1) {
            $fail('Metadata EXIF harus dihapus sebelum gambar diupload.');
        }
    }

    private function hasCleanEnding(string $contents, int $type): bool
    {
        if ($type === IMAGETYPE_JPEG) {
            return str_ends_with($contents, "\xFF\xD9");
        }

        if ($type === IMAGETYPE_PNG) {
            return str_starts_with($contents, "\x89PNG\r\n\x1A\n")
                && str_ends_with($contents, "\x00\x00\x00\x00IEND\xAE\x42\x60\x82");
        }

        if ($type === IMAGETYPE_WEBP) {
            $declaredSize = unpack('Vsize', substr($contents, 4, 4));

            return str_starts_with($contents, 'RIFF')
                && substr($contents, 8, 4) === 'WEBP'
                && is_array($declaredSize)
                && ((int) ($declaredSize['size'] ?? -1) + 8) === strlen($contents);
        }

        return false;
    }
}
