<?php

namespace App\Support\Media;

use Illuminate\Http\UploadedFile;

final class ImageDimensions
{
    /** @return array{width: int, height: int}|null */
    public function fromUploadedFile(UploadedFile $file): ?array
    {
        $path = $file->getRealPath();

        if (! is_string($path) || $path === '') {
            return null;
        }

        $dimensions = @getimagesize($path);

        if (! is_array($dimensions)) {
            return null;
        }

        $width = (int) ($dimensions[0] ?? 0);
        $height = (int) ($dimensions[1] ?? 0);

        if ($width < 1 || $height < 1) {
            return null;
        }

        return [
            'width' => $width,
            'height' => $height,
        ];
    }
}
