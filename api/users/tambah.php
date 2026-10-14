<?php
require __DIR__ . '/../includes/auth.php';
require_role('petugas');
$page_title = "Tambah Akun Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Hanya anggota yang belum punya akun
$tersedia = $pdo->query(
    "SELECT a.id, a.nama, a.no_anggota
     FROM anggota a
     WHERE NOT EXISTS (SELECT 1 FROM users u WHERE u.anggota_id = a.id)
     ORDER BY a.nama"
)->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Tambah Akun Anggota</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <?php if (empty($tersedia)): ?>
                <p class="flash flash-error">Semua anggota sudah punya akun. <a href="/anggota/tambah">Tambahkan data anggota</a> terlebih dahulu.</p>
            <?php else: ?>
            <form method="post" action="/users/proses_tambah">
                <?php echo csrf_field(); ?>
                <p>
                    <label for="anggota_id">Anggota</label><br>
                    <select id="anggota_id" name="anggota_id" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php foreach ($tersedia as $a): ?>
                        <option value="<?php echo (int) $a['id']; ?>"><?php echo e($a['nama']); ?> (<?php echo e($a['no_anggota']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required pattern="[A-Za-z0-9_.\-]{3,50}" title="3-50 karakter: huruf, angka, titik, garis bawah, strip">
                </p>
                <p>
                    <label for="password">Password Awal</label><br>
                    <input type="password" id="password" name="password" required minlength="8">
                </p>
                <p>
                    <button type="submit">Buat Akun</button>
                    <a href="/users/list">Batal</a>
                </p>
            </form>
            <?php endif; ?>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
