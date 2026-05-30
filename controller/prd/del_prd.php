<?php
require_once __DIR__ . '/../../model/product.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/');
}


if (!isset($_SESSION['account_id'])) {
    header('Location: ' . BASE_URL . 'index.php'); exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$account_id = (int)$_SESSION['account_id'];

if ($product_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid product.';
    header('Location: ' . BASE_URL . 'view/dashboard.php'); exit;
}

$product = new Product();
$deleted  = $product->delete($product_id, $account_id);

$_SESSION[$deleted ? 'flash_success' : 'flash_error'] = $deleted
    ? 'Product deleted successfully.'
    : 'Product could not be deleted or was not found.';

header('Location: ' . BASE_URL . 'view/dashboard.php');
exit;
