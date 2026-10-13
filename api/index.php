<?php
require __DIR__ . '/includes/auth.php';
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$totalDipinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
?>
        <section class="hero">
            <span class="eyebrow">Perpustakaan Digital</span>
            <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
            <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="buku/list.php">Lihat Koleksi Buku</a>
                <?php if ($sudahLogin): ?>
                <a class="btn btn-outline" href="peminjaman/tambah.php">Peminjaman Baru</a>
                <?php else: ?>
                <a class="btn btn-outline" href="auth/login.php">Login Petugas</a>
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
            <article class="stat-card">
                <p><?php echo $totalAnggota; ?></p>
                <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a3 3 0 0 0-3-3"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/></svg></div>
                <h3>Total Anggota</h3>
            </article>
            <article class="stat-card">
                <p><?php echo $totalDipinjam; ?></p>
                <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div>
                <h3>Sedang Dipinjam</h3>
            </article>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
