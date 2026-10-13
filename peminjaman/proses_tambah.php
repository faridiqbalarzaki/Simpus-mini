<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$anggotaId = $_POST['anggota_id'] ?? '';
$bukuId = $_POST['buku_id'] ?? '';

if ($anggotaId === '' || $bukuId === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota dan buku wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // =========================================================================
    // VALIDASI BISNIS (JS12 & JS13):
    // Jika anggota memiliki peminjaman yang statusnya 'dipinjam' dan sudah
    // melewati batas waktu > 14 hari dari tanggal_jatuh_tempo, maka sistem
    // harus menolak anggota tersebut dari meminjam buku baru.
    // =========================================================================
    $colCheck = $pdo->prepare("
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'peminjaman' AND column_name = 'tanggal_jatuh_tempo'
    ");
    $colCheck->execute();
    $hasJatuhTempo = (bool) $colCheck->fetch();

    if ($hasJatuhTempo) {
        $cekTerlambat = $pdo->prepare("
            SELECT COUNT(*) FROM peminjaman
            WHERE anggota_id = :anggota_id
              AND status = 'dipinjam'
              AND (
                (tanggal_jatuh_tempo IS NOT NULL AND (CURRENT_DATE - tanggal_jatuh_tempo) > 14)
                OR
                (tanggal_jatuh_tempo IS NULL AND (CURRENT_DATE - tanggal_pinjam) > 14)
              )
        ");
    } else {
        $cekTerlambat = $pdo->prepare("
            SELECT COUNT(*) FROM peminjaman
            WHERE anggota_id = :anggota_id
              AND status = 'dipinjam'
              AND (CURRENT_DATE - tanggal_pinjam) > 14
        ");
    }

    $cekTerlambat->execute(['anggota_id' => $anggotaId]);
    if ((int) $cekTerlambat->fetchColumn() > 0) {
        throw new Exception('Peminjaman ditolak: Anggota masih memiliki peminjaman buku yang telah melewati batas waktu lebih dari 14 hari dari tanggal jatuh tempo. Harap kembalikan buku terlebih dahulu.');
    }

    // Kunci baris buku (FOR UPDATE) agar stok tidak berubah oleh transaksi lain
    // di tengah proses ini — mencegah stok menjadi negatif akibat race condition.
    $cek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $bukuId]);
    $buku = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$buku || $buku['stok'] < 1) {
        throw new Exception('Stok buku tidak tersedia.');
    }

    if ($hasJatuhTempo) {
        $insert = $pdo->prepare(
            "INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
             VALUES (:buku_id, :anggota_id, CURRENT_DATE, CURRENT_DATE + 14, 'dipinjam')"
        );
    } else {
        $insert = $pdo->prepare(
            "INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, status)
             VALUES (:buku_id, :anggota_id, CURRENT_DATE, 'dipinjam')"
        );
    }
    $insert->execute(['buku_id' => $bukuId, 'anggota_id' => $anggotaId]);

    $update = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id");
    $update->execute(['id' => $bukuId]);

    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman berhasil dicatat.'];
    header('Location: ../index.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat peminjaman: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
