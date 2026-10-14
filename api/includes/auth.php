<?php
// Guard clause: di-include di baris paling atas halaman yang butuh login.
// Untuk pembatasan role, lanjutkan dengan: require_role('petugas');
require_once __DIR__ . '/rbac.php';
require_login();
