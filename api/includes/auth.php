<?php
// Guard clause: di-include di baris paling atas halaman yang butuh login.
require_once __DIR__ . '/session.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}
