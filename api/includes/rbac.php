<?php
// =====================================================================
// RBAC: otorisasi berbasis role (petugas / anggota).
// Role & status akun SELALU dibaca dari database pada tiap request yang
// butuh login (bukan dipercaya dari cookie), sehingga blokir akun dan
// perubahan role berlaku seketika, walau cookie session masih valid.
// =====================================================================
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

function rbac_db(): PDO
{
    if (!isset($GLOBALS['pdo'])) {
        require __DIR__ . '/koneksi.php';
    }
    return $GLOBALS['pdo'];
}

// Data user yang sedang login (null jika belum login / dihapus / diblokir).
function auth_user(): ?array
{
    static $loaded = false;
    static $user = null;
    if ($loaded) {
        return $user;
    }
    $loaded = true;

    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }

    $stmt = rbac_db()->prepare(
        'SELECT id, nama, username, role, aktif, anggota_id FROM users WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION = [];
        return null;
    }

    $aktif = in_array($row['aktif'], [true, 't', 'true', '1', 1], true);
    if (!$aktif) {
        $_SESSION = ['flash' => [
            'type'  => 'error',
            'pesan' => 'Akun Anda diblokir. Silakan hubungi petugas perpustakaan.',
        ]];
        return null;
    }

    // Sinkronkan cookie dengan kondisi terbaru di database
    $_SESSION['nama'] = $row['nama'];
    $_SESSION['role'] = $row['role'];

    $user = $row;
    return $user;
}

function require_login(): array
{
    $user = auth_user();
    if ($user === null) {
        header('Location: /auth/login');
        exit;
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        forbidden();
    }
    return $user;
}

function forbidden(string $pesan = 'Anda tidak memiliki izin untuk membuka halaman atau melakukan aksi ini.'): void
{
    http_response_code(403);
    $page_title = 'Akses Ditolak';
    include __DIR__ . '/header.php';
    echo '<section><h2>403 &mdash; Akses Ditolak</h2>'
        . '<p class="flash flash-error">' . e($pesan) . '</p>'
        . '<p><a class="btn btn-primary" href="/">Kembali ke Beranda</a></p></section>';
    include __DIR__ . '/footer.php';
    exit;
}
