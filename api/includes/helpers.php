<?php
// Membungkus output data yang berasal dari input pengguna sebelum
// dicetak ke HTML, untuk mencegah XSS (Cross-Site Scripting).
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Redirect dengan path absolut (aman untuk URL bersih di Vercel).
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
