<?php
require_once __DIR__ . '/frame/header.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

require_once __DIR__ . '/../model/account.php';

$account_id = (int)$_SESSION['account_id'];
$account    = new Account();
$row        = $account->findById($account_id);

if (!$row) { session_destroy(); header('Location: ' . BASE_URL . 'index.php'); exit; }

$flash_success = $flash_error = '';
if (!empty($_SESSION['flash_success'])) { $flash_success = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (!empty($_SESSION['flash_error']))   { $flash_error   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }

$initials = strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1));
$val      = fn(string $k): string => htmlspecialchars($row[$k] ?? '');
?>

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="page-title mb-1">
            <i class="bi bi-person-circle me-2" style="color:var(--violet-400);"></i>My Profile
        </h4>
        <p class="page-subtitle mb-0">Manage your account details and security settings</p>
    </div>
    <a href="<?= BASE_URL ?>view/dashboard.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
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

<div class="row g-4">

    <!-- Left: Avatar + quick info -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4 d-flex flex-column align-items-center text-center">

                <div class="profile-avatar mb-3"><?= $initials ?></div>

                <div class="fw-bold fs-5" style="font-family:'Syne',sans-serif;color:var(--text-primary);">
                    <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                </div>
                <div class="small mt-1" style="color:var(--text-muted);">
                    <?= htmlspecialchars($row['email']) ?>
                </div>

                <hr class="w-100 my-3">

                <div class="w-100 text-start">
                    <div class="info-block mb-2">
                        <div class="label">Contact</div>
                        <div class="value"><?= htmlspecialchars($row['contact'] ?: '—') ?></div>
                    </div>
                    <div class="info-block mb-2">
                        <div class="label">Hire Date</div>
                        <div class="value">
                            <?= $row['hire_date'] ? date('M d, Y', strtotime($row['hire_date'])) : '—' ?>
                        </div>
                    </div>
                    <div class="info-block">
                        <div class="label">Member Since</div>
                        <div class="value">
                            <?= isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A' ?>
                        </div>
                    </div>
                </div>

                <hr class="w-100 my-3">

                <!-- Vertical quick actions -->
                <div class="action-btns w-100">
                    <a href="#editForm" class="btn btn-outline-brand">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                    <a href="#passwordForm" class="btn btn-outline-secondary">
                        <i class="bi bi-shield-lock me-1"></i>Change Password
                    </a>
                    <a href="<?= BASE_URL ?>controller/acc/logout.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Right: Forms -->
    <div class="col-12 col-lg-8">

        <!-- Edit Profile -->
        <div class="card mb-4" id="editForm">
            <div class="card-header-custom">
                <i class="bi bi-person-fill me-2"></i>Edit Profile
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>controller/acc/accUpd.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="update">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= $val('first_name') ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= $val('last_name') ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" class="form-control" id="contact" name="contact"
                                       value="<?= $val('contact') ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" id="hire_date" name="hire_date"
                                       value="<?= $val('hire_date') ?>" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= $val('email') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-4" style="max-width:220px;">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-floppy me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4" id="passwordForm">
            <div class="card-header-custom">
                <i class="bi bi-shield-lock me-2"></i>Change Password
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>controller/acc/accUpd.php" method="POST" novalidate>
                    <input type="hidden" name="action"      value="update">
                    <input type="hidden" name="first_name"  value="<?= $val('first_name') ?>">
                    <input type="hidden" name="last_name"   value="<?= $val('last_name') ?>">
                    <input type="hidden" name="contact"     value="<?= $val('contact') ?>">
                    <input type="hidden" name="email"       value="<?= $val('email') ?>">
                    <input type="hidden" name="hire_date"   value="<?= $val('hire_date') ?>">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="new_password"
                                       name="new_password" placeholder="Min. 8 characters">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password"
                                       name="confirm_password" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-4" style="max-width:220px;">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-shield-check me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="danger-zone">
            <h6 class="mb-1" style="font-family:'Syne',sans-serif;color:var(--danger);">
                <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
            </h6>
            <p class="small mb-3" style="color:var(--text-muted);">
                Permanently delete your account and all associated inventory data. This cannot be undone.
            </p>
            <form action="<?= BASE_URL ?>controller/acc/accUpd.php" method="POST"
                  onsubmit="return confirm('⚠️ Delete your account permanently?\n\nAll your inventory data will be lost. This cannot be undone.')">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-person-x me-1"></i>Delete My Account
                </button>
            </form>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>
