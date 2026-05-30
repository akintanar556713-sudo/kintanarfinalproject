<?php
require_once __DIR__ . '/../../model/account.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Build BASE_URL for redirects
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$root_dir  = dirname(dirname(dirname(__FILE__)));
$doc_root  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$base_path = rtrim(str_replace(['\\', $doc_root], ['/', ''], $root_dir), '/');
$base      = $scheme . '://' . $host . $base_path . '/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base . 'index.php'); exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['flash_error'] = 'Email and password are required.';
    header('Location: ' . $base . 'index.php'); exit;
}

$account = new Account();
$row     = $account->findByEmail($email);

if (!$row || !password_verify($password, $row['password'])) {
    $_SESSION['flash_error'] = 'Invalid email or password.';
    header('Location: ' . $base . 'index.php'); exit;
}

session_regenerate_id(true);
$_SESSION['account_id'] = $row['account_id'];
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name']  = $row['last_name'];
$_SESSION['email']      = $row['email'];

header('Location: ' . $base . 'view/dashboard.php');
exit;
