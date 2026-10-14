<?php
// Profil milik akun yang sedang login (petugas maupun anggota).
// Hanya data akun sendiri: id diambil dari session, bukan dari URL.
require __DIR__ . '/../includes/auth.php';
$page_title = "Profil Saya";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$anggota = null;
if (!empty($user['anggota_id'])) {
    $stmt = $pdo->prepare("SELECT nama, no_anggota, alamat, no_hp FROM anggota WHERE id = :id");
    $stmt->execute(['id' => $user['anggota_id']]);
    $anggota = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
?>
        <section>
            <h2>Profil Saya</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <form method="post" action="/profil/proses_edit">
                <?php echo csrf_field(); ?>
                <p>
                    <label>Username</label><br>
                    <input type="text" value="<?php echo e($user['username']); ?>" disabled>
                </p>
                <p>
                    <label>Role</label><br>
                    <input type="text" value="<?php echo e($user['role']); ?>" disabled>
                </p>
                <?php if ($anggota): ?>
                <p>
                    <label>No. Anggota</label><br>
                    <input type="text" value="<?php echo e($anggota['no_anggota']); ?>" disabled>
                </p>
                <?php endif; ?>
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" value="<?php echo e($user['nama']); ?>" required maxlength="255">
                </p>
                <?php if ($anggota): ?>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <input type="text" id="alamat" name="alamat" value="<?php echo e($anggota['alamat']); ?>" maxlength="255">
                </p>
                <p>
                    <label for="no_hp">No. HP</label><br>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo e($anggota['no_hp']); ?>" maxlength="30">
                </p>
                <?php endif; ?>

                <h3>Ganti Password <small>(kosongkan jika tidak diubah)</small></h3>
                <p>
                    <label for="password_lama">Password Saat Ini</label><br>
                    <input type="password" id="password_lama" name="password_lama" autocomplete="current-password">
                </p>
                <p>
                    <label for="password_baru">Password Baru</label><br>
                    <input type="password" id="password_baru" name="password_baru" minlength="8" autocomplete="new-password">
                </p>
                <p>
                    <label for="password_konfirmasi">Konfirmasi Password Baru</label><br>
                    <input type="password" id="password_konfirmasi" name="password_konfirmasi" minlength="8" autocomplete="new-password">
                </p>
                <p>
                    <button type="submit">Simpan Perubahan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
