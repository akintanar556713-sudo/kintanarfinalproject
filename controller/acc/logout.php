<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$root_dir  = dirname(dirname(dirname(__FILE__)));
$doc_root  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$base_path = rtrim(str_replace(['\\', $doc_root], ['/', ''], $root_dir), '/');
$base      = $scheme . '://' . $host . $base_path . '/';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

header('Location: ' . $base . 'index.php');
exit;
