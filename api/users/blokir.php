<?php
// Blokir / aktifkan kembali akun ANGGOTA. Akun petugas tidak bisa diubah dari sini.
require __DIR__ . '/../includes/auth.php';
$petugas = require_role('petugas');
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /users/list');
    exit;
}
csrf_verify();

$id   = (int) ($_POST['id'] ?? 0);
$aksi = $_POST['aksi'] ?? '';

if ($id === (int) $petugas['id']) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anda tidak dapat memblokir akun sendiri.'];
} elseif ($aksi === 'blokir' || $aksi === 'aktifkan') {
    // Literal TRUE/FALSE (bukan bind boolean) agar aman dengan emulated prepares
    $sql = $aksi === 'blokir'
        ? "UPDATE users SET aktif = FALSE WHERE id = :id AND role = 'anggota'"
        : "UPDATE users SET aktif = TRUE WHERE id = :id AND role = 'anggota'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $_SESSION['flash'] = $stmt->rowCount() > 0
        ? ['type' => 'success', 'pesan' => $aksi === 'blokir' ? 'Akun berhasil diblokir.' : 'Akun berhasil diaktifkan kembali.']
        : ['type' => 'error', 'pesan' => 'Akun anggota tidak ditemukan.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Aksi tidak valid.'];
}

header('Location: /users/list');
exit;
