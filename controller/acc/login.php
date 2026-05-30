<?php
require_once __DIR__ . '/../../model/account.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php'); exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['flash_error'] = 'Email and password are required.';
    header('Location: ' . BASE_URL . 'index.php'); exit;
}

$account = new Account();
$row     = $account->findByEmail($email);

if (!$row || !password_verify($password, $row['password'])) {
    $_SESSION['flash_error'] = 'Invalid email or password.';
    header('Location: ' . BASE_URL . 'index.php'); exit;
}

session_regenerate_id(true);
$_SESSION['account_id'] = $row['account_id'];
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name']  = $row['last_name'];
$_SESSION['email']      = $row['email'];

header('Location: ' . BASE_URL . 'view/dashboard.php');
exit;
