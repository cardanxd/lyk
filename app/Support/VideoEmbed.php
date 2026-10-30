<?php

namespace App\Support;

/** Convierte enlaces de YouTube o Facebook en la URL segura para insertar el video. */
class VideoEmbed
{
    private const YOUTUBE_HOSTS = ['youtube.com', 'www.youtube.com', 'm.youtube.com'];

    private const FACEBOOK_HOSTS = ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com'];

    public static function fromUrl(string $url): ?string
    {
        $parts = parse_url(trim($url));
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
        $isFacebookVideo = (in_array($host, self::FACEBOOK_HOSTS, true) && (
                preg_match('#/(videos|reel)/\d+/?$#', $path)
                || (preg_match('#^/watch/?$#', $path) && is_string($query['v'] ?? null) && ctype_digit($query['v']))
            ))
            || ($host === 'fb.watch' && preg_match('#^/[A-Za-z0-9_-]+/?$#', $path));

        if ($isFacebookVideo) {
            return 'https://www.facebook.com/plugins/video.php?href='.rawurlencode(trim($url)).'&show_text=false';
        }

        return null;
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
