<?php
// read.php — require'd by dashboard.php, never accessed directly.

if (!isset($_SESSION['account_id'])) {
    $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $root_dir  = dirname(dirname(dirname(__FILE__)));
    $doc_root  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $base_path = rtrim(str_replace(['\\', $doc_root], ['/', ''], $root_dir), '/');
    header('Location: ' . $scheme . '://' . $host . $base_path . '/index.php');
    exit;
}

require_once __DIR__ . '/../../model/product.php';

$product = new Product();

$filters = [
    'search'   => trim($_GET['search']   ?? ''),
    'category' => trim($_GET['category'] ?? ''),
    'status'   => trim($_GET['status']   ?? ''),
];

$account_id = (int)$_SESSION['account_id'];
$products   = $product->getAll($account_id, $filters);
$categories = $product->getCategories($account_id);
$stats      = $product->getStats($account_id);
