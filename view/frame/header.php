<?php
// header.php — Shared page header for Asinstorage

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolve correct relative path to style.css from any view depth
$depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;
$root  = str_repeat('../', max(0, $depth - 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asinstorage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>view/style.css">
</head>
<body>

<?php if (isset($_SESSION['account_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">

        <a class="navbar-brand" href="<?= $root ?>view/dashboard.php">
            Asin<span class="accent">storage</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], 'dashboard') ? 'active' : '' ?>"
                       href="<?= $root ?>view/dashboard.php">
                        <i class="bi bi-boxes me-1"></i>Inventory
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], 'profile') ? 'active' : '' ?>"
                       href="<?= $root ?>view/profile.php">
                        <i class="bi bi-person-circle me-1"></i>Profile
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- User info pill -->
                <div class="d-none d-lg-flex align-items-center gap-2"
                     style="background:rgba(130,87,229,0.1);border:1px solid var(--border);
                            border-radius:30px;padding:0.35rem 1rem;">
                    <div style="width:28px;height:28px;border-radius:50%;
                                background:linear-gradient(135deg,var(--violet-800),var(--violet-500));
                                display:flex;align-items:center;justify-content:center;
                                font-family:'Syne',sans-serif;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                        <?= strtoupper(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-size:.8rem;font-weight:600;color:var(--text-primary);line-height:1.2;">
                            <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?>
                        </div>
                        <div style="font-size:.68rem;color:var(--text-muted);line-height:1.2;">
                            <?= htmlspecialchars($_SESSION['email']) ?>
                        </div>
                    </div>
                </div>

                <a href="<?= $root ?>controller/acc/logout.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="page-content">
<div class="container-fluid px-4">
