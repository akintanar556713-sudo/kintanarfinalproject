<?php
require_once __DIR__ . '/../../model/product.php';

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

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$account_id = (int)$_SESSION['account_id'];

if ($product_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid product.';
    header('Location: ' . $base . 'view/dashboard.php'); exit;
}

$product = new Product();
$deleted  = $product->delete($product_id, $account_id);

$_SESSION[$deleted ? 'flash_success' : 'flash_error'] = $deleted
    ? 'Product deleted successfully.'
    : 'Product could not be deleted or was not found.';

header('Location: ' . $base . 'view/dashboard.php');
exit;
