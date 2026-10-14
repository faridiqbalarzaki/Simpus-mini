<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Beranda";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

if ($isPetugas) {
    $row = $pdo->query(
        "SELECT
            (SELECT COUNT(*) FROM buku) AS total_buku,
            (SELECT COUNT(*) FROM anggota) AS total_anggota,
            (SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam') AS total_dipinjam"
    )->fetch(PDO::FETCH_ASSOC);
} else {
    // Anggota: hanya total katalog + pinjaman aktif MILIKNYA sendiri
    $stmt = $pdo->prepare(
        "SELECT
            (SELECT COUNT(*) FROM buku) AS total_buku,
            0 AS total_anggota,
            (SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam' AND anggota_id = :aid) AS total_dipinjam"
    );
    $stmt->execute(['aid' => (int) ($user['anggota_id'] ?? 0)]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
$totalBuku = $row['total_buku'];
$totalAnggota = $row['total_anggota'];
$totalDipinjam = $row['total_dipinjam'];
?>
        <section class="hero">
            <span class="eyebrow">Perpustakaan Digital</span>
            <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
            <p><?php echo $isPetugas ? 'Kelola buku, anggota, dan sirkulasi perpustakaan.' : 'Telusuri katalog buku dan pantau peminjaman Anda.'; ?></p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="/buku/list">Lihat Koleksi Buku</a>
                <?php if ($isPetugas): ?>
                <a class="btn btn-outline" href="/peminjaman/tambah">Peminjaman Baru</a>
                <?php elseif ($isAnggota): ?>
                <a class="btn btn-outline" href="/peminjaman/riwayat">Riwayat Saya</a>
                <?php else: ?>
                <a class="btn btn-outline" href="/auth/login">Login Petugas</a>
                <?php endif; ?>
            </div>
        </section>

        <h3 class="stats-label">Ringkasan</h3>
        <section class="stats">
            <article class="stat-card">
                <p><?php echo $totalBuku; ?></p>
                <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
                <h3>Total Buku</h3>
            </article>
            <?php if ($isPetugas): ?>
            <article class="stat-card">
                <p><?php echo $totalAnggota; ?></p>
                <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a3 3 0 0 0-3-3"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/></svg></div>
                <h3>Total Anggota</h3>
            </article>
            <?php endif; ?>
            <article class="stat-card">
                <p><?php echo $totalDipinjam; ?></p>
                <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div>
                <h3><?php echo $isPetugas ? 'Sedang Dipinjam' : 'Pinjaman Saya'; ?></h3>
            </article>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>