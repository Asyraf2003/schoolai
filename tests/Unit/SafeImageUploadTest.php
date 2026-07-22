<?php

use App\Rules\SafeImageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

function uploadedPng(string $name = 'clean.png', string $suffix = ''): UploadedFile
{
    $contents = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
        true
    );
    $path = tempnam(sys_get_temp_dir(), 'safe-image-');

    file_put_contents($path, $contents.$suffix);

    return new UploadedFile($path, $name, 'image/png', null, true);
}

function uploadedJpegWithExif(string $name = 'camera-photo.jpg'): UploadedFile
{
    $file = UploadedFile::fake()->image($name, 32, 32);
    $path = $file->getRealPath();
    $contents = file_get_contents($path);

    // APP1 payload with a minimal EXIF header. Length includes the two-byte
    // length field itself, so 0x0010 describes 14 bytes of payload.
    $exifSegment = "\xFF\xE1\x00\x10Exif\x00\x00".str_repeat("\x00", 8);
    file_put_contents($path, substr($contents, 0, 2).$exifSegment.substr($contents, 2));

    return $file;
}

it('accepts a clean raster image whose extension and content match', function (): void {
    $validator = Validator::make(
        ['image' => uploadedPng()],
        ['image' => [new SafeImageUpload]]
    );

    expect($validator->passes())->toBeTrue();
});

it('accepts a valid jpeg that contains ordinary exif metadata', function (): void {
    $validator = Validator::make(
        ['image' => uploadedJpegWithExif()],
        ['image' => [new SafeImageUpload]]
    );

    expect($validator->passes())->toBeTrue();
});

it('rejects active data appended to an otherwise valid image', function (): void {
    $validator = Validator::make(
        ['image' => uploadedPng('payload.png', '<?php echo "owned";')],
        ['image' => [new SafeImageUpload]]
    );

    expect($validator->fails())->toBeTrue();
});

it('rejects a fake client extension', function (): void {
    $validator = Validator::make(
        ['image' => uploadedPng('fake.jpg')],
        ['image' => [new SafeImageUpload]]
    );

    expect($validator->fails())->toBeTrue();
});

it('rejects an image with an excessive declared dimension', function (): void {
    $file = uploadedPng();
    $contents = file_get_contents($file->getRealPath());

    // PNG IHDR width starts at byte 16. getimagesize reads the declaration
    // before decoding pixels, which lets this cover decompression-bomb limits.
    $contents = substr_replace($contents, pack('N', 9000), 16, 4);
    file_put_contents($file->getRealPath(), $contents);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new SafeImageUpload]]
    );

    expect($validator->fails())->toBeTrue();
});
