<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /profil/index');
    exit;
}
csrf_verify();

$me = require_login();   // id SELALU dari session/DB, tidak pernah dari input form

$nama   = trim($_POST['nama'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp   = trim($_POST['no_hp'] ?? '');
$pwLama = $_POST['password_lama'] ?? '';
$pwBaru = $_POST['password_baru'] ?? '';
$pwKonf = $_POST['password_konfirmasi'] ?? '';

$errors = [];
if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}

$gantiPassword = ($pwBaru !== '' || $pwLama !== '' || $pwKonf !== '');
if ($gantiPassword) {
    $row = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $row->execute(['id' => $me['id']]);
    $hash = (string) $row->fetchColumn();

    if (!password_verify($pwLama, $hash)) {
        $errors[] = 'Password saat ini salah.';
    }
    if (strlen($pwBaru) < 8) {
        $errors[] = 'Password baru minimal 8 karakter.';
    }
    if ($pwBaru !== $pwKonf) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: /profil/index');
    exit;
}

try {
    $pdo->beginTransaction();

    // Hanya kolom nama & password; role/aktif/anggota_id TIDAK bisa diubah dari sini
    if ($gantiPassword) {
        $pdo->prepare("UPDATE users SET nama = :nama, password = :pw WHERE id = :id")
            ->execute(['nama' => $nama, 'pw' => password_hash($pwBaru, PASSWORD_DEFAULT), 'id' => $me['id']]);
    } else {
        $pdo->prepare("UPDATE users SET nama = :nama WHERE id = :id")
            ->execute(['nama' => $nama, 'id' => $me['id']]);
    }

    if (!empty($me['anggota_id'])) {
        $pdo->prepare("UPDATE anggota SET nama = :nama, alamat = :alamat, no_hp = :no_hp WHERE id = :aid")
            ->execute(['nama' => $nama, 'alamat' => $alamat, 'no_hp' => $noHp, 'aid' => $me['anggota_id']]);
    }

    $pdo->commit();
    $_SESSION['nama'] = $nama;
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Profil berhasil diperbarui.'];
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('profil/proses_edit: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal menyimpan profil. Coba lagi.'];
}

header('Location: /profil/index');
exit;
