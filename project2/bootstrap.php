<?php
/** Bootstrap shared by pages that need sessions, validation or the database. */
require_once __DIR__ . '/settings.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}
require_once __DIR__ . '/functions.php';