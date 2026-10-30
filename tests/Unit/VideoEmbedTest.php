<?php

use App\Support\VideoEmbed;

it('convierte enlaces de YouTube', function (string $url) {
    expect(VideoEmbed::fromUrl($url))->toBe('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
})->with([
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtube.com/watch?v=dQw4w9WgXcQ&t=10s',
    'https://m.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/shorts/dQw4w9WgXcQ',
]);

it('convierte enlaces de Facebook', function (string $url) {
    expect(VideoEmbed::fromUrl($url))
        ->toBe('https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=false');
})->with([
    'https://www.facebook.com/lyk/videos/123456789',
    'https://fb.watch/abcDEF123/',
    'https://www.facebook.com/reel/123456789',
]);

it('rechaza enlaces que no son de YouTube ni Facebook', function (string $url) {
    expect(VideoEmbed::fromUrl($url))->toBeNull();
})->with([
    'https://evil.com/x',
    'javascript:alert(1)',
    'https://youtube.com.evil.com/watch?v=dQw4w9WgXcQ',
    'https://www.youtube.com/watch?v=<script>',
    'https://evil.com/?u=https://www.facebook.com/x/videos/1',
    'http://www.facebook.com/lyk/photos/1',
]);

it('rechaza enlaces de Facebook sin id de video', function (string $url) {
    expect(VideoEmbed::fromUrl($url))->toBeNull();
})->with([
    'https://www.facebook.com/watch',
    'https://www.facebook.com/videos-de-algo',
    'https://www.facebook.com/lyk/videos/',
]);

it('acepta Facebook watch con v=', fn () =>
    expect(VideoEmbed::fromUrl('https://www.facebook.com/watch/?v=123456'))->not->toBeNull());
