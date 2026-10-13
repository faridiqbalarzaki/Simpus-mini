<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buat token CSRF dan simpan di Cookie peramban
function csrf_token() {
    if (empty($_COOKIE['csrf_token'])) {
        $token = bin2hex(random_bytes(32));
        setcookie('csrf_token', $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        $_COOKIE['csrf_token'] = $token;
    }
    return $_COOKIE['csrf_token'];
}

// Output field input tersembunyi untuk form
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Verifikasi token dari POST dengan Cookie
function csrf_verify() {
    $postToken = $_POST['csrf_token'] ?? '';
    $cookieToken = $_COOKIE['csrf_token'] ?? '';

    if (empty($postToken) || empty($cookieToken) || !hash_equals($cookieToken, $postToken)) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.'];
        
        // Arahkan kembali ke halaman asal (register/login)
        $referer = $_SERVER['HTTP_REFERER'] ?? '/auth/login.php';
        header('Location: ' . $referer);
        exit;
    }
}