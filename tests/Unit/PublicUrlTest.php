<?php

use App\Models\PpdbSetting;
use App\Support\PublicUrl;

it('accepts only public HTTP and HTTPS targets without credentials', function (): void {
    expect(PublicUrl::isSafe('https://93.184.216.34/article'))
        ->toBeTrue()
        ->and(PublicUrl::isSafe('http://93.184.216.34:8080/path'))
        ->toBeTrue()
        ->and(PublicUrl::isSafe('ftp://93.184.216.34/file'))
        ->toBeFalse()
        ->and(PublicUrl::isSafe('javascript:alert(1)'))
        ->toBeFalse()
        ->and(PublicUrl::isSafe('https://user:password@93.184.216.34/path'))
        ->toBeFalse();
});

it('rejects private reserved and alternate IP representations', function (string $url): void {
    expect(PublicUrl::isSafe($url))->toBeFalse();
})->with([
    'IPv4 loopback' => 'http://127.0.0.1',
    'IPv4 shortened loopback' => 'http://127.1',
    'IPv4 decimal integer' => 'http://2130706433',
    'IPv4 hexadecimal' => 'http://0x7f000001',
    'IPv4 octal' => 'http://017700000001',
    'IPv4 link local' => 'http://169.254.169.254/latest/meta-data',
    'IPv4 private class A' => 'http://10.0.0.1',
    'IPv4 private class B' => 'http://172.16.0.1',
    'IPv4 private class C' => 'http://192.168.0.1',
    'IPv6 loopback' => 'http://[::1]',
    'IPv6 link local' => 'http://[fe80::1]',
    'IPv6 unique local' => 'http://[fc00::1]',
]);

it('rejects punycode and DNS names that resolve to any private address', function (): void {
    expect(PublicUrl::isSafe('https://xn--pple-43d.com'))
        ->toBeFalse()
        ->and(PublicUrl::isSafe('https://public.example', [], ['127.0.0.1']))
        ->toBeFalse()
        ->and(PublicUrl::isSafe('https://public.example', [], ['93.184.216.34', '10.0.0.2']))
        ->toBeFalse()
        ->and(PublicUrl::isSafe('https://public.example', [], ['93.184.216.34']))
        ->toBeTrue();
});

it('blocks application-sensitive paths and protects PPDB legacy data', function (): void {
    expect(PublicUrl::isSafe('https://93.184.216.34/admin/users', ['/admin']))
        ->toBeFalse();

    $setting = new PpdbSetting([
        'registration_url' => 'http://[::1]/registration',
        'information_url' => 'https://user:pass@93.184.216.34/info',
        'is_active' => true,
    ]);

    expect($setting->publicRegistrationUrl())
        ->toBeNull()
        ->and($setting->publicInformationUrl())
        ->toBeNull()
        ->and($setting->isRegistrationOpen())
        ->toBeFalse();
});
