<?php

namespace App\Support;

/** Convierte enlaces de YouTube o Facebook en la URL segura para insertar el video. */
class VideoEmbed
{
    public const SHARE_LINK = 'Facebook no permite mostrar en otras páginas los enlaces cortos de “Compartir” (/share/…). Abre el video en Facebook y copia la dirección completa de la barra del navegador (contiene /videos/ o /reel/), o pega el código de “Insertar” del video.';

    private const YOUTUBE_HOSTS = ['youtube.com', 'www.youtube.com', 'm.youtube.com'];

    private const FACEBOOK_HOSTS = ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com'];

    /** Acepta un enlace de video o el código <iframe> de "Insertar" de YouTube/Facebook. */
    public static function fromUrl(string $input): ?string
    {
        $url = self::extractIframeSrc($input) ?? trim($input);
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        if ($id = self::youtubeId($host, $path, $parts['query'] ?? '')) {
            return "https://www.youtube-nocookie.com/embed/{$id}";
        }

        parse_str($parts['query'] ?? '', $query);

        // URL del reproductor de Facebook (la del código "Insertar"): se valida el video que trae en href.
        if (in_array($host, self::FACEBOOK_HOSTS, true) && $path === '/plugins/video.php') {
            $href = is_string($query['href'] ?? null) ? $query['href'] : '';

            return self::isFacebookVideo($href) ? self::facebookEmbed($href) : null;
        }

        return self::isFacebookVideo($url) ? self::facebookEmbed($url) : null;
    }

    public static function isFacebookShareLink(string $url): bool
    {
        $parts = parse_url(trim($url));

        return in_array(strtolower($parts['host'] ?? ''), self::FACEBOOK_HOSTS, true)
            && preg_match('#^/share/[vr]/#', $parts['path'] ?? '') === 1;
    }

    private static function isFacebookVideo(string $url): bool
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        if ($host === 'fb.watch') {
            return preg_match('#^/[A-Za-z0-9_-]+/?$#', $path) === 1;
        }

        return in_array($host, self::FACEBOOK_HOSTS, true) && (
            preg_match('#/(videos|reel)/(?:[^/]+/)?\d+/?$#', $path) === 1
            || (preg_match('#^/watch/?$#', $path) === 1 && is_string($query['v'] ?? null) && ctype_digit($query['v']))
        );
    }

    private static function facebookEmbed(string $url): string
    {
        return 'https://www.facebook.com/plugins/video.php?href='.rawurlencode(trim($url)).'&show_text=false';
    }

    private static function extractIframeSrc(string $input): ?string
    {
        if (! preg_match('#<iframe\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1#is', $input, $m)) {
            return null;
        }

        return html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5);
    }

    private static function youtubeId(string $host, string $path, string $query): ?string
    {
        $id = null;

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
        } elseif (in_array($host, self::YOUTUBE_HOSTS, true)) {
            if ($path === '/watch') {
                parse_str($query, $params);
                $id = $params['v'] ?? null;
            } elseif (preg_match('#^/(shorts|embed)/([^/]+)#', $path, $m)) {
                $id = $m[2];
            }
        }

        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }
}
