<?php
// read.php — Product list controller.
// Require'd by dashboard.php — not accessed directly.

if (!isset($_SESSION['account_id'])) {
    header('Location: ../../index.php');
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
