<?php
require_once __DIR__ . '/../../model/product.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';


if (!isset($_SESSION['account_id'])) {
    header('Location: ' . BASE_URL . 'index.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'view/dashboard.php'); exit;
}

$account_id = (int)$_SESSION['account_id'];
$product_id = isset($_POST['product_id']) && $_POST['product_id'] !== ''
              ? (int)$_POST['product_id'] : null;

$sku          = trim($_POST['sku']          ?? '');
$product_name = trim($_POST['product_name'] ?? '');
$category     = trim($_POST['category']     ?? '');
$description  = trim($_POST['description']  ?? '');
$supplier     = trim($_POST['supplier']     ?? '');
$quantity     = trim($_POST['quantity']     ?? '');
$unit_price   = trim($_POST['unit_price']   ?? '');
$status       = trim($_POST['status']       ?? 'active');

$errors = [];
if (empty($sku))          $errors[] = 'SKU is required.';
if (empty($product_name)) $errors[] = 'Product name is required.';
if (empty($category))     $errors[] = 'Category is required.';
if ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) $errors[] = 'Quantity must be a non-negative number.';
if ($unit_price === '' || !is_numeric($unit_price) || (float)$unit_price < 0) $errors[] = 'Unit price must be a non-negative number.';

if (!in_array($status, ['active','inactive','low_stock'], true)) $status = 'active';

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['flash_old']   = compact('sku','product_name','category','description','supplier','quantity','unit_price','status');
    $redirect = ($product_id !== null)
        ? BASE_URL . 'view/add_upd.php?id=' . $product_id
        : BASE_URL . 'view/add_upd.php';
    header('Location: ' . $redirect); exit;
}

$prd       = new Product();
$is_update = ($product_id !== null) && $prd->exists($product_id, $account_id);

$prd->account_id   = $account_id;
$prd->sku          = $sku;
$prd->product_name = $product_name;
$prd->category     = $category;
$prd->description  = $description;
$prd->supplier     = $supplier;
$prd->quantity     = (int)$quantity;
$prd->unit_price   = (float)$unit_price;
$prd->status       = $status;

if ($is_update) {
    $prd->product_id = $product_id;
    $success = $prd->update();
    $msg_ok  = 'Product updated successfully.';
    $msg_err = 'Failed to update product.';
} else {
    $success = $prd->create();
    $msg_ok  = 'Product added successfully.';
    $msg_err = 'Failed to add product.';
}

$_SESSION[$success ? 'flash_success' : 'flash_error'] = $success ? $msg_ok : $msg_err;
header('Location: ' . BASE_URL . 'view/dashboard.php');
exit;
