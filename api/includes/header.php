<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
$sudahLogin = isset($_SESSION['user_id']);

// Prefix relatif ke root proyek
$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

// Halaman yang sedang dibuka untuk menandai menu aktif
$__halaman = ($__rel === '' ? '' : $__rel . '/') . basename($_SERVER['SCRIPT_FILENAME']);
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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- CSS Utama (Otomatis memuat warna hijau dari var(--bar)) -->
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
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
                    <a href="<?php echo $base; ?>index.php"<?php echo $aktif(['index.php']); ?>>
                        <i class="ph ph-house menu-icon"></i> Beranda
                    </a>
                </li>

                <li class="nav-category">Koleksi Buku</li>
                <li>
                    <a href="<?php echo $base; ?>buku/list.php"<?php echo $aktif(['buku/list.php', 'buku/edit.php']); ?>>
                        <i class="ph ph-books menu-icon"></i> Daftar Buku
                    </a>
                </li>
                <?php if ($sudahLogin): ?>
                <li>
                    <a href="<?php echo $base; ?>buku/tambah.php"<?php echo $aktif(['buku/tambah.php']); ?>>
                        <i class="ph ph-plus-circle menu-icon"></i> Tambah Buku
                    </a>
                </li>

                <li class="nav-category">Keanggotaan</li>
                <li>
                    <a href="<?php echo $base; ?>anggota/list.php"<?php echo $aktif(['anggota/list.php', 'anggota/edit.php']); ?>>
                        <i class="ph ph-users menu-icon"></i> Daftar Anggota
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base; ?>anggota/tambah.php"<?php echo $aktif(['anggota/tambah.php']); ?>>
                        <i class="ph ph-user-plus menu-icon"></i> Tambah Anggota
                    </a>
                </li>

                <li class="nav-category">Sirkulasi</li>
                <li>
                    <a href="<?php echo $base; ?>peminjaman/tambah.php"<?php echo $aktif(['peminjaman/tambah.php']); ?>>
                        <i class="ph ph-tray-arrow-down menu-icon"></i> Peminjaman Baru
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base; ?>peminjaman/kembali.php"<?php echo $aktif(['peminjaman/kembali.php']); ?>>
                        <i class="ph ph-tray-arrow-up menu-icon"></i> Pengembalian
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base; ?>peminjaman/riwayat.php"<?php echo $aktif(['peminjaman/riwayat.php']); ?>>
                        <i class="ph ph-clock-counter-clockwise menu-icon"></i> Riwayat
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
                        <span class="user-name" title="<?php echo e($_SESSION['nama']); ?>"><?php echo e($_SESSION['nama']); ?></span>
                    </div>
                    <a href="<?php echo $base; ?>auth/logout.php" class="btn-logout" title="Logout">
                        <i class="ph ph-sign-out"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base; ?>auth/login.php" class="btn-login">
                        <i class="ph ph-sign-in"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <div class="app-layout">
        <main>