<?php
// ==========================================
// 1. LOGIKA ROUTER VERCEL
// ==========================================
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rawurldecode(preg_replace('/^\/api/', '', $path));

if ($path !== '' && $path !== '/') {

    // --- a. Sajikan file statis dari folder assets (CSS, JS, gambar, font) ---
    if (strpos($path, '/assets/') === 0) {
        $base = realpath(__DIR__ . '/assets');
        $real = realpath(__DIR__ . $path);

        // realpath + cek prefix = cegah path traversal (../)
        if ($real && is_file($real) && strpos($real, $base . DIRECTORY_SEPARATOR) === 0) {
            $mimes = [
                'css'   => 'text/css; charset=utf-8',
                'js'    => 'application/javascript; charset=utf-8',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'svg'   => 'image/svg+xml',
                'webp'  => 'image/webp',
                'ico'   => 'image/x-icon',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf'   => 'font/ttf',
            ];
            $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
            header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
            header('Cache-Control: public, max-age=3600');
            readfile($real);
            exit;
        }
        http_response_code(404);
        exit;
    }

    // --- b. Halaman PHP (buku/, auth/, dst) ---
    $real = realpath(__DIR__ . $path);
    $root = realpath(__DIR__);
    $blocked = ['/includes/', '/sql/', '/docs/', '/Dokumentasi/'];

    $forbidden = false;
    foreach ($blocked as $b) {
        if (strpos($path, $b) === 0) { $forbidden = true; }
    }

    if (!$forbidden && $real && strpos($real, $root) === 0) {
        if (is_file($real) && pathinfo($real, PATHINFO_EXTENSION) === 'php') {
            require $real;
            exit;
        } elseif (is_dir($real) && file_exists($real . '/index.php')) {
            require $real . '/index.php';
            exit;
        }
    }

    http_response_code(404);
    echo "<h1>404 Halaman Tidak Ditemukan</h1>";
    exit;
}