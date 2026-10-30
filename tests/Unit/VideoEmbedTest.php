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

it('acepta videos de Facebook con título en la dirección', function (string $url) {
    expect(VideoEmbed::fromUrl($url))
        ->toBe('https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=false');
})->with([
    'https://www.facebook.com/lyk/videos/modalidad-40-explicada/123456789/',
    'https://www.facebook.com/61564424277456/videos/asesor%C3%ADa-y-consultoria-lyk-te-acompa%C3%B1a-en-tu-proceso-de-retiro-por-desempleo/1744235962840460/',
    'https://www.facebook.com/reel/1744235962840460',
]);

it('rechaza los enlaces cortos de Compartir porque Facebook no los reproduce insertados', function (string $url) {
    expect(VideoEmbed::fromUrl($url))->toBeNull()
        ->and(VideoEmbed::isFacebookShareLink($url))->toBeTrue();
})->with([
    'https://www.facebook.com/share/v/1GPfBQogox/',
    'https://www.facebook.com/share/r/1GPfBQogox/',
    'https://m.facebook.com/share/v/1GPfBQogox',
]);

it('acepta el código de Insertar de Facebook', function () {
    $code = '<iframe src="https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1744235962840460%2F&show_text=false&width=267&t=0" width="267" height="476" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true"></iframe>';
    expect(VideoEmbed::fromUrl($code))
        ->toBe('https://www.facebook.com/plugins/video.php?href='.rawurlencode('https://www.facebook.com/reel/1744235962840460/').'&show_text=false');
});

it('acepta la URL del reproductor de Facebook', fn () =>
    expect(VideoEmbed::fromUrl('https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Flyk%2Fvideos%2F123456%2F'))
        ->toBe('https://www.facebook.com/plugins/video.php?href='.rawurlencode('https://www.facebook.com/lyk/videos/123456/').'&show_text=false'));

it('rechaza un código de Insertar con un video que no es de Facebook', function (string $code) {
    expect(VideoEmbed::fromUrl($code))->toBeNull();
})->with([
    '<iframe src="https://evil.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1%2F"></iframe>',
    '<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fevil.com%2Freel%2F1%2F"></iframe>',
    '<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fshare%2Fv%2Fabc%2F"></iframe>',
]);

it('rechaza enlaces cortos de Facebook que no son video', function (string $url) {
    expect(VideoEmbed::fromUrl($url))->toBeNull();
})->with([
    'https://www.facebook.com/share/p/1GPfBQogox/',
    'https://www.facebook.com/share/v/',
]);
