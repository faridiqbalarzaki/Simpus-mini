<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Hitung path relatif ke auth/login.php berdasarkan kedalaman folder pemanggil
    $rootDir = dirname(__DIR__);
    $scriptDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);
    $rel = ltrim(str_replace('\\', '/', substr($scriptDir, strlen($rootDir))), '/');
    $base = $rel === '' ? '' : str_repeat('../', substr_count($rel, '/') + 1);

    header('Location: ' . $base . 'auth/login.php');
    exit;
}
