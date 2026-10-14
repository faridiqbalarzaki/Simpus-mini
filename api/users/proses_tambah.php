<?php
require __DIR__ . '/../includes/auth.php';
require_role('petugas');
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /users/tambah');
    exit;
}
csrf_verify();

$anggotaId = (int) ($_POST['anggota_id'] ?? 0);
$username  = trim($_POST['username'] ?? '');
$password  = $_POST['password'] ?? '';

$errors = [];
if (!preg_match('/^[A-Za-z0-9_.\-]{3,50}$/', $username)) {
    $errors[] = 'Username 3-50 karakter (huruf, angka, titik, garis bawah, strip).';
}
if (strlen($password) < 8) {
    $errors[] = 'Password minimal 8 karakter.';
}

$stmt = $pdo->prepare("SELECT id, nama FROM anggota WHERE id = :id");
$stmt->execute(['id' => $anggotaId]);
$anggota = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$anggota) {
    $errors[] = 'Anggota tidak ditemukan.';
}

if (empty($errors)) {
    $cek = $pdo->prepare("SELECT 1 FROM users WHERE username = :u OR anggota_id = :a");
    $cek->execute(['u' => $username, 'a' => $anggotaId]);
    if ($cek->fetch()) {
        $errors[] = 'Username sudah dipakai, atau anggota ini sudah punya akun.';
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: /users/tambah');
    exit;
}

try {
    $ins = $pdo->prepare(
        "INSERT INTO users (nama, username, password, role, aktif, anggota_id)
         VALUES (:nama, :username, :password, 'anggota', TRUE, :anggota_id)"
    );
    $ins->execute([
        'nama'       => $anggota['nama'],
        'username'   => $username,
        'password'   => password_hash($password, PASSWORD_DEFAULT),
        'anggota_id' => $anggotaId,
    ]);
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Akun anggota berhasil dibuat.'];
    header('Location: /users/list');
} catch (PDOException $e) {
    // Mis. username/anggota ganda akibat dua petugas menyimpan bersamaan
    error_log('users/proses_tambah: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal membuat akun. Username atau anggota sudah terpakai.'];
    header('Location: /users/tambah');
}
exit;
