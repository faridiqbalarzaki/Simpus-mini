<?php
require __DIR__ . '/../includes/auth.php';
require_role('petugas');
$page_title = "Akun Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$akun = $pdo->query(
    "SELECT u.id, u.nama, u.username, u.aktif, a.no_anggota
     FROM users u
     LEFT JOIN anggota a ON a.id = u.anggota_id
     WHERE u.role = 'anggota'
     ORDER BY u.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <div class="section-head">
                <div>
                    <h2>Akun Anggota</h2>
                    <p class="lead">Kelola akun login anggota: tambah, blokir, atau aktifkan kembali.</p>
                </div>
                <a class="btn btn-primary" href="/users/tambah">+ Tambah Akun</a>
            </div>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>No. Anggota</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($akun)): ?>
                    <tr><td colspan="5">Belum ada akun anggota.</td></tr>
                    <?php else: ?>
                        <?php foreach ($akun as $a): ?>
                        <?php $aktif = in_array($a['aktif'], [true, 't', 'true', '1', 1], true); ?>
                        <tr>
                            <td class="judul"><?php echo e($a['nama']); ?></td>
                            <td><?php echo e($a['username']); ?></td>
                            <td><?php echo e($a['no_anggota'] ?? '-'); ?></td>
                            <td><span class="badge <?php echo $aktif ? 'badge-ok' : 'badge-out'; ?>"><?php echo $aktif ? 'Aktif' : 'Diblokir'; ?></span></td>
                            <td class="aksi">
                                <form class="form-hapus" method="post" action="/users/blokir">
                                    <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                                    <input type="hidden" name="aksi" value="<?php echo $aktif ? 'blokir' : 'aktifkan'; ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="<?php echo $aktif ? 'btn-hapus' : 'btn-edit'; ?>"><?php echo $aktif ? 'Blokir' : 'Aktifkan'; ?></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
