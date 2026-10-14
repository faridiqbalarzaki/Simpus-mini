<?php
// Koneksi lazy + sekali per request (aman di-require berkali-kali,
// termasuk dari dalam fungsi, karena disimpan juga di $GLOBALS['pdo']).
if (isset($GLOBALS['pdo'])) {
    $pdo = $GLOBALS['pdo'];
} else {
    $config = require __DIR__ . '/config.php';

    try {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s;connect_timeout=5',
            $config['db_host'], $config['db_port'], $config['db_name'], $config['db_sslmode']
        );
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // WAJIB untuk Supabase pooler mode transaction (port 6543): tidak
            // mendukung server-side prepared statement. Bonus: 1 round-trip
            // per query, bukan 2 (prepare + execute).
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]);
        $GLOBALS['pdo'] = $pdo;
    } catch (PDOException $e) {
        error_log('DB connect failed: ' . $e->getMessage());
        http_response_code(503);
        die('Layanan database sedang tidak tersedia. Coba lagi sebentar.');
    }
}
