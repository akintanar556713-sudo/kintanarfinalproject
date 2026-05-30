<?php
require_once __DIR__ . '/frame/header.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../model/product.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$account_id = (int)$_SESSION['account_id'];

if ($product_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$product = new Product();
$p       = $product->getById($product_id, $account_id);

if (!$p) {
    $_SESSION['flash_error'] = 'Product not found or access denied.';
    header('Location: dashboard.php');
    exit;
}

$status_label = ucfirst(str_replace('_', ' ', $p['status']));
$qty          = (int)$p['quantity'];
$qty_class    = $qty === 0 ? 'qty-zero' : ($p['status'] === 'low_stock' ? 'qty-low' : 'qty-ok');
?>

<!-- ── Breadcrumb ────────────────────────────────────────────── -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Inventory</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($p['product_name']) ?></li>
    </ol>
</nav>

<!-- ── Product Card ─────────────────────────────────────────── -->
<div class="card">
    <div class="card-header-custom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <span class="fw-bold fs-5"><?= htmlspecialchars($p['product_name']) ?></span>
            <span class="ms-3 small" style="color:rgba(255,255,255,0.35);font-family:monospace;">
                <?= htmlspecialchars($p['sku']) ?>
            </span>
        </div>
        <span class="badge badge-<?= htmlspecialchars($p['status']) ?>" style="font-size:.8rem;">
            <?= $status_label ?>
        </span>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">

            <!-- ── Left: details ────────────────────────────── -->
            <div class="col-12 col-lg-7">

                <!-- Description -->
                <div class="mb-4">
                    <div class="section-label">Description</div>
                    <p class="mb-0" style="white-space:pre-wrap;color:var(--text-secondary);">
                        <?= !empty($p['description'])
                            ? htmlspecialchars($p['description'])
                            : '<em style="color:var(--text-faint);">No description provided.</em>' ?>
                    </p>
                </div>

                <!-- Category & Supplier -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="info-block">
                            <div class="label">Category</div>
                            <div class="value"><?= htmlspecialchars($p['category']) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-block">
                            <div class="label">Supplier</div>
                            <div class="value"><?= htmlspecialchars($p['supplier'] ?: '—') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="info-block">
                            <div class="label">Date Added</div>
                            <div class="value"><?= date('M d, Y', strtotime($p['date_added'])) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-block">
                            <div class="label">Last Updated</div>
                            <div class="value"><?= date('M d, Y  h:i A', strtotime($p['last_updated'])) ?></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Right: stats panel ─────────────────────── -->
            <div class="col-12 col-lg-5">
                <div class="stats-panel">

                    <!-- Price -->
                    <div class="mb-3">
                        <div class="label">Unit Price</div>
                        <div class="display-6 fw-bold" style="color:var(--violet-200);">
                            ₱<?= number_format((float)$p['unit_price'], 2) ?>
                        </div>
                    </div>

                    <hr class="panel-divider">

                    <!-- Quantity -->
                    <div class="mb-3">
                        <div class="label">Stock Quantity</div>
                        <div class="fs-3 fw-bold <?= $qty_class ?>">
                            <?= number_format($qty) ?>
                            <span class="fs-6 fw-normal" style="color:rgba(255,255,255,.3);">units</span>
                        </div>
                    </div>

                    <hr class="panel-divider">

                    <!-- Total value -->
                    <div class="mb-4">
                        <div class="label">Total Value</div>
                        <div class="fw-semibold" style="color:var(--text-secondary);">
                            ₱<?= number_format($qty * (float)$p['unit_price'], 2) ?>
                        </div>
                    </div>

                    <!-- Vertical action buttons -->
                    <div class="action-btns">
                        <a href="add_upd.php?id=<?= (int)$p['product_id'] ?>"
                           class="btn btn-brand">
                            <i class="bi bi-pencil me-1"></i>Edit Product
                        </a>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                        </a>
                        <a href="../controller/prd/del_prd.php?id=<?= (int)$p['product_id'] ?>"
                           class="btn btn-outline-danger"
                           onclick="return confirm('Delete \'<?= addslashes(htmlspecialchars($p['product_name'])) ?>\'?\nThis cannot be undone.')">
                            <i class="bi bi-trash me-1"></i>Delete Product
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>
