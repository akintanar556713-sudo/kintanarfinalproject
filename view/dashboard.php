<?php
require_once __DIR__ . '/frame/header.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

require_once __DIR__ . '/../controller/prd/read.php';

$flash_success = $flash_error = '';
if (!empty($_SESSION['flash_success'])) { $flash_success = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (!empty($_SESSION['flash_error']))   { $flash_error   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }

$total_value = number_format((float)($stats['total_value'] ?? 0), 2);
?>

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="page-title mb-1">
            <i class="bi bi-boxes me-2" style="color:var(--violet-400);"></i>Product Inventory
        </h4>
        <p class="page-subtitle mb-0">
            Welcome back, <strong style="color:var(--violet-300);"><?= htmlspecialchars($_SESSION['first_name']) ?></strong>
        </p>
    </div>
    <a href="<?= BASE_URL ?>view/add_upd.php" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
</div>

<?php if ($flash_success): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 small mb-4" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($flash_success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 small mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($flash_error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-value"><?= number_format((int)($stats['total'] ?? 0)) ?></div>
            <i class="bi bi-box-seam stat-icon" style="color:var(--violet-400);"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Active</div>
            <div class="stat-value" style="color:var(--success);"><?= number_format((int)($stats['active'] ?? 0)) ?></div>
            <i class="bi bi-check-circle stat-icon" style="color:var(--success);"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Low Stock</div>
            <div class="stat-value" style="color:var(--warning);"><?= number_format((int)($stats['low_stock'] ?? 0)) ?></div>
            <i class="bi bi-exclamation-triangle stat-icon" style="color:var(--warning);"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Inventory Value</div>
            <div class="stat-value" style="font-size:1.3rem;">₱<?= $total_value ?></div>
            <i class="bi bi-cash-coin stat-icon" style="color:var(--violet-400);"></i>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar mb-4">
    <form method="GET" action="<?= BASE_URL ?>view/dashboard.php" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label">Search</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search"
                       placeholder="Name or SKU…"
                       value="<?= htmlspecialchars($filters['search']) ?>">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Category</label>
            <select class="form-select form-select-sm" name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= $filters['category'] === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label">Status</label>
            <select class="form-select form-select-sm" name="status">
                <option value="">All</option>
                <option value="active"    <?= $filters['status'] === 'active'    ? 'selected' : '' ?>>Active</option>
                <option value="inactive"  <?= $filters['status'] === 'inactive'  ? 'selected' : '' ?>>Inactive</option>
                <option value="low_stock" <?= $filters['status'] === 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-brand btn-sm px-3">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="<?= BASE_URL ?>view/dashboard.php" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-x-lg me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Product Table -->
<div class="card">
    <div class="card-header-custom d-flex align-items-center justify-content-between">
        <span><i class="bi bi-table me-2"></i>Products</span>
        <span class="badge" style="background:rgba(130,87,229,0.2);color:var(--violet-300);border:1px solid var(--border);">
            <?= count($products) ?> result<?= count($products) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5 empty-state">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <div class="mb-1">No products found.</div>
                <?php if (array_filter($filters)): ?>
                    <a href="<?= BASE_URL ?>view/dashboard.php" class="small">Clear filters</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>view/add_upd.php" class="btn btn-brand btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i>Add your first product
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $p): ?>
                        <?php
                            $qty       = (int)$p['quantity'];
                            $qty_class = $qty === 0 ? 'qty-zero' : ($p['status'] === 'low_stock' ? 'qty-low' : '');
                        ?>
                        <tr>
                            <td class="small" style="color:var(--text-muted);font-family:monospace;">
                                <?= htmlspecialchars($p['sku']) ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>view/prd_view.php?id=<?= (int)$p['product_id'] ?>"
                                   style="color:var(--text-primary);font-weight:600;">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light"><?= htmlspecialchars($p['category']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="<?= $qty_class ?>" style="font-weight:600;">
                                    <?= number_format($qty) ?>
                                </span>
                            </td>
                            <td class="text-end" style="font-weight:600;">
                                ₱<?= number_format((float)$p['unit_price'], 2) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($p['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                </span>
                            </td>
                            <td class="small" style="color:var(--text-muted);">
                                <?= htmlspecialchars($p['supplier'] ?: '—') ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="<?= BASE_URL ?>view/prd_view.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    <a href="<?= BASE_URL ?>view/add_upd.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-brand">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <a href="<?= BASE_URL ?>controller/prd/del_prd.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete \'<?= addslashes(htmlspecialchars($p['product_name'])) ?>\'?\nThis cannot be undone.')">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>
