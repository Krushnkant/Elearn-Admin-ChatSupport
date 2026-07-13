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

// This file emulates Apache's "mod_rewrite" for the built-in PHP web server.
// This app is served with the document root at the PROJECT ROOT (asset URLs
// include the "public/" prefix, e.g. /public/Admin/dist/js/adminlte.min.js),
// so resolve static files relative to __DIR__ rather than __DIR__.'/public'.
// Directories are never served statically; they fall through to Laravel.
if ($uri !== '/' && is_file(__DIR__.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
