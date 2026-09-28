<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// --- Static asset handling (permanent fix for the daily "all CSS/JS 404" bug) ---
//
// Asset URLs in this app carry a historical "public/" prefix, e.g.
//   /public/Admin/dist/css/adminlte.min.css
// The real files live at  <project root>/public/Admin/...
//
// This router is used two ways and must work for BOTH so assets never 404
// again regardless of how the server was started:
//   1. php -S 127.0.0.1:9000 server.php   (document root = project ROOT)
//   2. php artisan serve                  (Laravel chdirs doc root to public/)
//
// Instead of returning false (which hands off to PHP's built-in static
// handler, and that handler resolves against whatever it thinks the doc root
// is -> 404 under artisan serve), we resolve the file ourselves against known
// locations and stream it. Directories always fall through to Laravel.
if ($uri !== '/') {
    $candidates = [
        __DIR__ . $uri,              // doc root = project root: /public/Admin/... -> public/Admin/...
        __DIR__ . '/public' . $uri,  // doc root = public/, URL without prefix: /Admin/... -> public/Admin/...
    ];
    // A URL that already contains the "public/" prefix, resolved from inside public/.
    if (strncmp($uri, '/public/', 8) === 0) {
        $candidates[] = __DIR__ . '/public' . substr($uri, 7);
    }

    foreach ($candidates as $file) {
        if (is_file($file)) {
            $mimes = [
                'css'   => 'text/css',
                'js'    => 'application/javascript',
                'mjs'   => 'application/javascript',
                'json'  => 'application/json',
                'map'   => 'application/json',
                'svg'   => 'image/svg+xml',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'webp'  => 'image/webp',
                'ico'   => 'image/x-icon',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf'   => 'font/ttf',
                'eot'   => 'application/vnd.ms-fontobject',
                'otf'   => 'font/otf',
                'pdf'   => 'application/pdf',
                'txt'   => 'text/plain',
                'html'  => 'text/html',
                'xml'   => 'application/xml',
                'mp4'   => 'video/mp4',
                'webm'  => 'video/webm',
            ];
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (isset($mimes[$ext])) {
                header('Content-Type: ' . $mimes[$ext]);
            }
            header('Content-Length: ' . filesize($file));
            header('Cache-Control: public, max-age=86400');
            readfile($file);
            exit;
        }
    }
}

require_once __DIR__ . '/public/index.php';
