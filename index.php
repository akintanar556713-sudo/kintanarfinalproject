<?php
// index.php — Login page (project root)

require_once __DIR__ . '/view/frame/header.php';

if (isset($_SESSION['account_id'])) {
    header('Location: ' . BASE_URL . 'view/dashboard.php');
    exit;
}

$error   = '';
$success = '';
if (!empty($_SESSION['flash_error']))   { $error   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }
if (!empty($_SESSION['flash_success'])) { $success = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
$deleted = isset($_GET['deleted']);
?>

<div class="auth-wrapper">
    <div class="auth-card card p-4 p-md-5">

        <div class="text-center mb-4">
            <div class="auth-logo mb-1">Asin<span>storage</span></div>
            <p class="small" style="color:var(--text-muted);">Inventory Management System</p>
        </div>

        <?php if ($deleted): ?>
            <div class="alert alert-success py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i>Your account has been deleted. Goodbye!
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>controller/acc/login.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="you@example.com" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
            </button>
        </form>

        <hr class="my-4">
        <p class="text-center small mb-0" style="color:var(--text-muted);">
            No account yet?
            <a href="<?= BASE_URL ?>view/register.php" class="fw-semibold">Register here</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/view/frame/footer.php'; ?>
