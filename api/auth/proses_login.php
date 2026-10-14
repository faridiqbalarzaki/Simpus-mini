<?php
require_once __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Akun diblokir petugas -> tolak login (dicek setelah password benar
    // agar tidak membocorkan status akun ke orang yang tidak punya password)
    if (!in_array($user['aktif'], [true, 't', 'true', '1', 1], true)) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Akun Anda diblokir. Silakan hubungi petugas perpustakaan.'];
        header('Location: /auth/login');
        exit;
    }

    // Session = cookie bertanda tangan (stateless): tidak ada session ID
    // yang bisa di-fixation, jadi session_regenerate_id() tidak diperlukan.
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];
    header('Location: /');
    exit;
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
header('Location: /auth/login');
exit;