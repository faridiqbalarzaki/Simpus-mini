<?php
// Registrasi publik DINONAKTIFKAN (sebelumnya siapa pun bisa mendaftar sebagai
// 'petugas' -> celah eskalasi hak akses). Akun anggota dibuat oleh petugas
// lewat menu "Akun Anggota" (/users/tambah).
header('Location: /auth/login');
exit;
