<?php
require_once __DIR__ . '/../includes/session.php';

// Kosongkan session -> shutdown handler di session.php menghapus cookie.
$_SESSION = [];
header('Location: /auth/login');
exit;
