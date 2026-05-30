<?php
require_once __DIR__ . '/frame/header.php';

if (isset($_SESSION['account_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';
if (!empty($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
if (!empty($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$old = $_SESSION['flash_old'] ?? [];
unset($_SESSION['flash_old']);

$val = fn(string $k): string => htmlspecialchars($old[$k] ?? '');
?>

<div class="auth-wrapper" style="align-items:flex-start; padding-top: 2rem;">
    <div class="auth-card auth-card--wide card p-4 p-md-5">

        <!-- Brand -->
        <div class="text-center mb-4">
            <div class="auth-logo mb-1">Asin<span>storage</span></div>
            <p class="small" style="color:var(--text-muted);">Create your account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="../controller/acc/accReg.php" method="POST" novalidate>

            <!-- Name row -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="first_name" class="form-label">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           placeholder="Juan" value="<?= $val('first_name') ?>" required>
                </div>
                <div class="col-6">
                    <label for="last_name" class="form-label">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           placeholder="Dela Cruz" value="<?= $val('last_name') ?>" required>
                </div>
            </div>

            <!-- Contact -->
            <div class="mb-3">
                <label for="contact" class="form-label">
                    Contact Number <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="text" class="form-control" id="contact" name="contact"
                           placeholder="+63 9XX XXX XXXX" value="<?= $val('contact') ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    Email Address <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="you@example.com" value="<?= $val('email') ?>" required>
                </div>
            </div>

            <!-- Hire Date -->
            <div class="mb-3">
                <label for="hire_date" class="form-label">
                    Hire Date <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                           value="<?= $val('hire_date') ?>" required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Min. 8 characters" required>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="confirm_password" class="form-label">
                    Confirm Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="confirm_password"
                           name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-person-plus me-1"></i>Create Account
            </button>
        </form>

        <hr class="my-4">

        <p class="text-center small mb-0" style="color:var(--text-muted);">
            Already have an account?
            <a href="../index.php" class="fw-semibold">Sign in</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>
