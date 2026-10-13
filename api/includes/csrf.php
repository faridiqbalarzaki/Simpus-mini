<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. BUAT COOKIE SEGERA SAAT FILE DIMUAT
// Karena file ini di-include di baris atas header.php, setcookie akan aman
// dari error "headers already sent" karena HTML belum dicetak.
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

// 2. Fungsi untuk mengambil token yang sudah ada
function csrf_token() {
    return $_COOKIE['csrf_token'] ?? '';
}

// 3. Output field input tersembunyi untuk form
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// 4. Verifikasi token dari POST dengan Cookie
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