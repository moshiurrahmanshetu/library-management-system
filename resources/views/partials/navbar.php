<?php
/**
 * Top navigation bar partial for authenticated pages.
 */

$name = $user['name'] ?? 'User';
$role = $user['role_name'] ?? 'Reader';
$profilePhoto = $user['profile_photo'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        <span class="navbar-brand mb-0 h1"><?= e(APP_NAME) ?></span>

        <div class="d-flex align-items-center ms-auto">
            <span class="badge bg-primary me-2"><?= e($role) ?></span>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if ($profilePhoto): ?>
                        <img src="<?= e(upload_url($profilePhoto)) ?>" alt="Profile" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                    <?php else: ?>
                        <i class="bi bi-person-circle me-2"></i>
                    <?php endif; ?>
                    <?= e($name) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('password/change') ?>"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?= base_url('logout') ?>" method="POST" class="dropdown-item p-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-link text-decoration-none text-dark w-100 text-start px-3 py-1">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
