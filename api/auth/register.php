<?php
require_once __DIR__ . '/../includes/session.php';
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$page_title = "Registrasi Petugas";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section class="auth">
            <h2>Registrasi Petugas</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form method="post" action="/auth/proses_register">
                <?php echo csrf_field(); ?>
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password</label><br>
                    <input type="password" id="password" name="password" required minlength="6">
                </p>
                <p>
                    <button type="submit">Daftar</button>
                </p>
            </form>
            <p>Sudah punya akun? <a href="/auth/login">Login di sini</a></p>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>