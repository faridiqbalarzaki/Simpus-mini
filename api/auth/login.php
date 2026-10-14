<?php
require_once __DIR__ . '/../includes/session.php';
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$page_title = "Login";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section class="auth">
            <h2>Login Petugas</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form method="post" action="/auth/proses_login">
                <?php echo csrf_field(); ?>
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password</label><br>
                    <input type="password" id="password" name="password" required>
                </p>
                <p>
                    <button type="submit">Masuk</button>
                </p>
            </form>
            <p>Belum punya akun? Hubungi petugas perpustakaan untuk dibuatkan akun.</p>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>