<?php
// =====================================================================
// SESSION STATELESS (cocok untuk Vercel serverless)
// $_SESSION disimpan di cookie yang ditandatangani HMAC-SHA256, bukan di
// file /tmp (yang tidak dibagi antar-instance lambda). Tanpa I/O disk,
// tanpa query DB. Kode lama yang memakai $_SESSION[...] tetap jalan.
// =====================================================================
if (defined('APP_SESSION_LOADED')) {
    return;
}
define('APP_SESSION_LOADED', true);

const SESS_COOKIE = 'simpus_sess';
const SESS_TTL    = 60 * 60 * 8; // 8 jam

function sess_secret(): string
{
    $s = getenv('SESSION_SECRET');
    if (!$s) {
        // Fallback agar tidak error; SANGAT disarankan set SESSION_SECRET di Vercel.
        $s = hash('sha256', 'simpus|' . getenv('DB_PASS') . '|' . getenv('DB_USER'));
    }
    return $s;
}

function sess_b64(string $raw): string
{
    return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
}

function sess_unb64(string $str): string
{
    return (string) base64_decode(strtr($str, '-_', '+/'), true);
}

function sess_encode(array $data): string
{
    $payload = sess_b64(json_encode(['d' => $data, 'exp' => time() + SESS_TTL]));
    return $payload . '.' . sess_b64(hash_hmac('sha256', $payload, sess_secret(), true));
}

function sess_decode(string $cookie): array
{
    if (substr_count($cookie, '.') !== 1) {
        return [];
    }
    [$payload, $sig] = explode('.', $cookie, 2);
    $expected = sess_b64(hash_hmac('sha256', $payload, sess_secret(), true));
    if (!hash_equals($expected, $sig)) {
        return [];
    }
    $obj = json_decode(sess_unb64($payload), true);
    if (!is_array($obj) || ($obj['exp'] ?? 0) < time() || !is_array($obj['d'] ?? null)) {
        return [];
    }
    return $obj['d'];
}

function sess_cookie_opts(int $expires): array
{
    $https = (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') || !empty($_SERVER['HTTPS']);
    return [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

$_SESSION = sess_decode($_COOKIE[SESS_COOKIE] ?? '');
$GLOBALS['__sess_snapshot'] = json_encode($_SESSION);

// Buffer output agar cookie masih bisa dikirim di akhir request
// (PHP menjalankan shutdown function SEBELUM mem-flush buffer).
ob_start();

register_shutdown_function(function () {
    if (headers_sent()) {
        return;
    }
    $now = json_encode($_SESSION ?? []);
    if ($now === $GLOBALS['__sess_snapshot']) {
        return; // tidak ada perubahan, jangan kirim ulang cookie
    }
    if (empty($_SESSION)) {
        setcookie(SESS_COOKIE, '', sess_cookie_opts(time() - 3600)); // logout
    } else {
        setcookie(SESS_COOKIE, sess_encode($_SESSION), sess_cookie_opts(time() + SESS_TTL));
    }
});
