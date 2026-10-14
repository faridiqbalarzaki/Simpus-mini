<?php
// Petugas : melihat SEMUA transaksi (boleh difilter per anggota).
// Anggota : HANYA riwayat miliknya sendiri; parameter anggota_id dari URL
//           diabaikan sepenuhnya (mencegah akses data anggota lain / IDOR).
require __DIR__ . '/../includes/auth.php';
$page_title = "Riwayat Peminjaman";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$riwayat = [];
$daftarAnggota = [];
$anggotaId = '';
$anggotaTerpilih = null;
$tanpaProfilAnggota = false;

$sql = "SELECT b.judul, a.nama AS nama_anggota, p.tanggal_pinjam, p.tanggal_kembali, p.status
        FROM peminjaman p
        JOIN buku b ON b.id = p.buku_id
        JOIN anggota a ON a.id = p.anggota_id";

if ($isPetugas) {
    $daftarAnggota = $pdo->query("SELECT id, nama FROM anggota ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
    $anggotaId = (string) ($_GET['anggota_id'] ?? '');

    if ($anggotaId !== '' && ctype_digit($anggotaId)) {
        $stmt = $pdo->prepare($sql . " WHERE p.anggota_id = :id ORDER BY p.tanggal_pinjam DESC, p.id DESC");
        $stmt->execute(['id' => (int) $anggotaId]);
    } else {
        $anggotaId = '';
        $stmt = $pdo->query($sql . " ORDER BY p.tanggal_pinjam DESC, p.id DESC LIMIT 200");
    }
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $aid = (int) ($user['anggota_id'] ?? 0);
    if ($aid > 0) {
        $stmt = $pdo->prepare($sql . " WHERE p.anggota_id = :id ORDER BY p.tanggal_pinjam DESC, p.id DESC");
        $stmt->execute(['id' => $aid]);
        $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $tanpaProfilAnggota = true;
    }
}
$kolom = $isPetugas ? 5 : 4;
?>
        <section>
            <div class="section-head">
                <div>
                    <h2><?php echo $isPetugas ? 'Semua Transaksi Peminjaman' : 'Riwayat Peminjaman Saya'; ?></h2>
                    <p class="lead"><?php echo $isPetugas ? 'Seluruh catatan sirkulasi (maks. 200 terbaru jika tanpa filter).' : 'Catatan peminjaman buku pada akun Anda.'; ?></p>
                </div>
            </div>

            <?php if ($isPetugas): ?>
            <form class="filter" method="get" action="/peminjaman/riwayat">
                <p>
                    <label for="anggota_id">Filter Anggota</label><br>
                    <select id="anggota_id" name="anggota_id">
                        <option value="">-- Semua Anggota --</option>
                        <?php foreach ($daftarAnggota as $anggota): ?>
                        <option value="<?php echo (int) $anggota['id']; ?>" <?php echo $anggotaId === (string) $anggota['id'] ? 'selected' : ''; ?>>
                            <?php echo e($anggota['nama']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <button type="submit">Tampilkan</button>
                </p>
            </form>
            <?php endif; ?>

            <?php if ($tanpaProfilAnggota): ?>
                <p class="flash flash-error">Akun Anda belum dihubungkan dengan data keanggotaan. Hubungi petugas.</p>
            <?php else: ?>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Buku</th>
                        <?php if ($isPetugas): ?><th>Anggota</th><?php endif; ?>
                        <th>Pinjam</th>
                        <th>Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="<?php echo $kolom; ?>">Belum ada riwayat peminjaman.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $r): ?>
                        <tr>
                            <td class="judul"><?php echo e($r['judul']); ?></td>
                            <?php if ($isPetugas): ?><td><?php echo e($r['nama_anggota']); ?></td><?php endif; ?>
                            <td><?php echo e($r['tanggal_pinjam']); ?></td>
                            <td><?php echo e($r['tanggal_kembali'] ?? '-'); ?></td>
                            <td><span class="badge <?php echo $r['status'] === 'dipinjam' ? 'badge-low' : 'badge-ok'; ?>"><?php echo $r['status'] === 'dipinjam' ? 'Dipinjam' : 'Selesai'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
