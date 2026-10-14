<?php
// =====================================================================
// ROUTER TUNGGAL (Vercel): semua request non-statis masuk ke sini.
// File statis (/assets/*) dilayani CDN dari folder /public, bukan lewat PHP.
// URL bersih: /buku/list  ->  api/buku/list.php  (".php" tetap diterima)
// =====================================================================

$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$path = preg_replace('#^/api(?=/|$)#', '', $path);   // buang awalan /api jika ada
$path = '/' . trim($path, '/');                      // normalisasi: "/buku/list"
$path = preg_replace('#\.php$#', '', $path);         // terima juga /buku/list.php
if ($path === '/index' || $path === '') {
    $path = '/';
}

// Tolak path traversal & folder internal (bukan halaman)
if (str_contains($path, '..') || preg_match('#^/(includes|sql|docs|Dokumentasi|pages)(/|$)#i', $path)) {
    http_response_code(404);
    exit('404 Not Found');
}

// Dipakai header.php untuk menandai menu aktif (SCRIPT_FILENAME selalu index.php di sini)
$GLOBALS['ROUTE_PATH'] = $path;

$target = ($path === '/')
    ? __DIR__ . '/pages/home.php'
    : __DIR__ . $path . '.php';

if (!is_file($target)) {
    http_response_code(404);
    exit('<h1>404 Halaman Tidak Ditemukan</h1>');
}

require $target;
