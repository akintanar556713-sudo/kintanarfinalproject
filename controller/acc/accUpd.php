<?php
require_once __DIR__ . '/../../model/account.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$root_dir  = dirname(dirname(dirname(__FILE__)));
$doc_root  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$base_path = rtrim(str_replace(['\\', $doc_root], ['/', ''], $root_dir), '/');
$base      = $scheme . '://' . $host . $base_path . '/';

if (!isset($_SESSION['account_id'])) {
    header('Location: ' . $base . 'index.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base . 'view/profile.php'); exit;
}

$account_id = (int)$_SESSION['account_id'];
$action     = $_POST['action'] ?? 'update';
$account    = new Account();

// ── Delete ────────────────────────────────────────────────────
if ($action === 'delete') {
    if ($account->delete($account_id)) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ' . $base . 'index.php?deleted=1');
    } else {
        $_SESSION['flash_error'] = 'Failed to delete account. Please try again.';
        header('Location: ' . $base . 'view/profile.php');
    }
    exit;
}

// ── Update ────────────────────────────────────────────────────
$fields = ['first_name', 'last_name', 'contact', 'email', 'hire_date'];
$data   = [];
foreach ($fields as $f) { $data[$f] = trim($_POST[$f] ?? ''); }
$new_password     = $_POST['new_password']     ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];
foreach ($fields as $f) {
    if (empty($data[$f])) $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' is required.';
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if ($account->emailExists($data['email'], $account_id)) $errors[] = 'That email is already in use.';

$change_password = false;
if (!empty($new_password)) {
    if (strlen($new_password) < 8)       $errors[] = 'New password must be at least 8 characters.';
    if ($new_password !== $confirm_password) $errors[] = 'Passwords do not match.';
    $change_password = true;
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    header('Location: ' . $base . 'view/profile.php'); exit;
}

$account->account_id = $account_id;
$account->first_name = $data['first_name'];
$account->last_name  = $data['last_name'];
$account->contact    = $data['contact'];
$account->email      = $data['email'];
$account->hire_date  = $data['hire_date'];
$account->password   = $new_password;

if ($account->update($change_password)) {
    $_SESSION['first_name']    = $data['first_name'];
    $_SESSION['last_name']     = $data['last_name'];
    $_SESSION['email']         = $data['email'];
    $_SESSION['flash_success'] = 'Profile updated successfully.';
} else {
    $_SESSION['flash_error'] = 'Failed to update profile. Please try again.';
}

header('Location: ' . $base . 'view/profile.php');
exit;
