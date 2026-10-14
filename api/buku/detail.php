<?php
// Detail buku: read-only, terbuka untuk semua pengunjung (anggota & petugas).
$page_title = "Detail Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = :id");
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    http_response_code(404);
}
?>
        <section>
            <div class="section-head">
                <div>
                    <h2>Detail Buku</h2>
                </div>
                <?php if ($isPetugas && $buku): ?>
                <a class="btn btn-primary" href="/buku/edit?id=<?php echo (int) $buku['id']; ?>">Edit Buku</a>
                <?php endif; ?>
            </div>

            <?php if (!$buku): ?>
                <p class="flash flash-error">Buku tidak ditemukan.</p>
            <?php else: ?>
            <div class="table-responsive">
            <table>
                <tbody>
                    <tr><th>Judul</th><td><?php echo e($buku['judul']); ?></td></tr>
                    <tr><th>Pengarang</th><td><?php echo e($buku['pengarang']); ?></td></tr>
                    <tr><th>Tahun</th><td><?php echo e($buku['tahun']); ?></td></tr>
                    <tr><th>ISBN</th><td><?php echo e($buku['isbn'] ?: '-'); ?></td></tr>
                    <tr><th>Kategori</th><td><?php echo e($buku['kategori'] ?: '-'); ?></td></tr>
                    <tr>
                        <th>Stok</th>
                        <td><span class="badge <?php echo $buku['stok'] <= 0 ? 'badge-out' : ($buku['stok'] <= 2 ? 'badge-low' : 'badge-ok'); ?>"><?php echo e($buku['stok']); ?></span></td>
                    </tr>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
            <p><a href="/buku/list">&larr; Kembali ke Daftar Buku</a></p>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
