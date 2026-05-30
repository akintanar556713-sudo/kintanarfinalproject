<?php
require_once __DIR__ . '/../../model/account.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'view/register.php'); exit;
}

$fields = ['first_name', 'last_name', 'contact', 'email', 'hire_date'];
$data   = [];
foreach ($fields as $f) { $data[$f] = trim($_POST[$f] ?? ''); }
$password         = $_POST['password']         ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];
foreach ($fields as $f) {
    if (empty($data[$f])) $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' is required.';
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
if (strlen($password) < 8)           $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
if (!empty($data['hire_date']) && !strtotime($data['hire_date'])) $errors[] = 'Hire date is not valid.';

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['flash_old']   = $data;
    header('Location: ' . BASE_URL . 'view/register.php'); exit;
}

$account = new Account();
if ($account->emailExists($data['email'])) {
    $_SESSION['flash_error'] = 'That email address is already registered.';
    $_SESSION['flash_old']   = $data;
    header('Location: ' . BASE_URL . 'view/register.php'); exit;
}

$account->first_name = $data['first_name'];
$account->last_name  = $data['last_name'];
$account->contact    = $data['contact'];
$account->email      = $data['email'];
$account->hire_date  = $data['hire_date'];
$account->password   = $password;

if ($account->create()) {
    $_SESSION['flash_success'] = 'Account created successfully. You can now sign in.';
    header('Location: ' . BASE_URL . 'index.php');
} else {
    $_SESSION['flash_error'] = 'Registration failed. Please try again.';
    $_SESSION['flash_old']   = $data;
    header('Location: ' . BASE_URL . 'view/register.php');
}
exit;
