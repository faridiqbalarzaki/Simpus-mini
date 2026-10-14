<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/rbac.php';

// Role diverifikasi ke database (bukan sekadar percaya isi cookie)
$user = auth_user();
$sudahLogin = $user !== null;
$isPetugas = $sudahLogin && $user['role'] === 'petugas';
$isAnggota = $sudahLogin && $user['role'] === 'anggota';

// Halaman aktif diambil dari router (SCRIPT_FILENAME selalu index.php di Vercel)
$__halaman = ltrim($GLOBALS['ROUTE_PATH'] ?? '/', '/');
$__halaman = ($__halaman === '') ? 'index' : $__halaman;   // "buku/list"
$aktif = function (array $daftar) use ($__halaman) {
    return in_array($__halaman, $daftar, true) ? ' class="active" aria-current="page"' : '';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPUS-Mini<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>

    <!-- Fonts & Icons Modern -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ikon: CSS statis versi pinned (non-blocking JS + tanpa redirect unpkg) -->
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css">

    <!-- CSS Utama (path absolut) -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="has-sidebar">
    <!-- Topbar Mobile -->
    <div class="mobile-topbar">
        <div class="brand">
            <i class="ph-fill ph-books brand-icon"></i>
            <strong>SIMPUS-Mini</strong>
        </div>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Buka menu navigasi">
            <i class="ph ph-list"></i>
        </button>
    </div>

    <!-- Sidebar Hijau -->
    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-brand">
            <i class="ph-fill ph-books brand-icon"></i>
            <h2>SIMPUS-Mini</h2>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li class="nav-category">Navigasi Utama</li>
                <li>
                    <a href="/"<?php echo $aktif(['index']); ?>>
                        <i class="ph ph-house menu-icon"></i> Beranda
                    </a>
                </li>

                <li class="nav-category">Koleksi Buku</li>
                <li>
                    <a href="/buku/list"<?php echo $aktif(['buku/list', 'buku/detail', 'buku/edit']); ?>>
                        <i class="ph ph-books menu-icon"></i> Daftar Buku
                    </a>
                </li>
                <?php if ($isPetugas): ?>
                <li>
                    <a href="/buku/tambah"<?php echo $aktif(['buku/tambah']); ?>>
                        <i class="ph ph-plus-circle menu-icon"></i> Tambah Buku
                    </a>
                </li>

                <li class="nav-category">Keanggotaan</li>
                <li>
                    <a href="/anggota/list"<?php echo $aktif(['anggota/list', 'anggota/edit']); ?>>
                        <i class="ph ph-users menu-icon"></i> Daftar Anggota
                    </a>
                </li>
                <li>
                    <a href="/anggota/tambah"<?php echo $aktif(['anggota/tambah']); ?>>
                        <i class="ph ph-user-plus menu-icon"></i> Tambah Anggota
                    </a>
                </li>
                <li>
                    <a href="/users/list"<?php echo $aktif(['users/list', 'users/tambah']); ?>>
                        <i class="ph ph-identification-card menu-icon"></i> Akun Anggota
                    </a>
                </li>

                <li class="nav-category">Sirkulasi</li>
                <li>
                    <a href="/peminjaman/tambah"<?php echo $aktif(['peminjaman/tambah']); ?>>
                        <i class="ph ph-tray-arrow-down menu-icon"></i> Peminjaman Baru
                    </a>
                </li>
                <li>
                    <a href="/peminjaman/kembali"<?php echo $aktif(['peminjaman/kembali']); ?>>
                        <i class="ph ph-tray-arrow-up menu-icon"></i> Pengembalian
                    </a>
                </li>
                <li>
                    <a href="/peminjaman/riwayat"<?php echo $aktif(['peminjaman/riwayat']); ?>>
                        <i class="ph ph-clock-counter-clockwise menu-icon"></i> Semua Transaksi
                    </a>
                </li>
                <?php elseif ($isAnggota): ?>
                <li class="nav-category">Peminjaman</li>
                <li>
                    <a href="/peminjaman/riwayat"<?php echo $aktif(['peminjaman/riwayat']); ?>>
                        <i class="ph ph-clock-counter-clockwise menu-icon"></i> Riwayat Saya
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($sudahLogin): ?>
                <li class="nav-category">Akun</li>
                <li>
                    <a href="/profil/index"<?php echo $aktif(['profil/index']); ?>>
                        <i class="ph ph-user-gear menu-icon"></i> Profil Saya
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="auth-status">
                <?php if ($sudahLogin): ?>
                    <div class="user-info">
                        <i class="ph-fill ph-user-circle" style="font-size: 1.5rem; color: #ffffff;"></i>
                        <span class="user-name" title="<?php echo e($user['nama']); ?>"><?php echo e($user['nama']); ?> <small style="opacity:.7">(<?php echo e($user['role']); ?>)</small></span>
                    </div>
                    <a href="/auth/logout" class="btn-logout" title="Logout">
                        <i class="ph ph-sign-out"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="/auth/login" class="btn-login">
                        <i class="ph ph-sign-in"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <div class="app-layout">
        <main>