<?php
/**
 * Sidebar partial for authenticated pages.
 */

$currentUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$menuItems = [
    ['uri' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
    ['uri' => 'profile', 'label' => 'Profile', 'icon' => 'bi-person'],
    ['uri' => 'password/change', 'label' => 'Change Password', 'icon' => 'bi-shield-lock'],
];
?>

<aside class="bg-dark text-white vh-100" id="sidebar" style="min-width: 260px; max-width: 260px;">
    <div class="d-flex align-items-center justify-content-center py-4 border-bottom border-secondary">
        <i class="bi bi-book-half fs-3 me-2"></i>
        <span class="fw-bold fs-5">Library MS</span>
    </div>

    <nav class="nav flex-column px-3 py-3">
        <?php foreach ($menuItems as $item): ?>
            <?php $isActive = $currentUri === $item['uri'] ? 'active bg-primary' : 'text-white'; ?>
            <a href="<?= base_url($item['uri']) ?>" class="nav-link rounded mb-2 <?= $isActive ?>">
                <i class="bi <?= $item['icon'] ?> me-2"></i>
                <?= e($item['label']) ?>
            </a>
        <?php endforeach; ?>

        <hr class="text-secondary my-3">

        <form action="<?= base_url('logout') ?>" method="POST" class="d-grid">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light btn-sm text-start">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </button>
        </form>
    </nav>
</aside>
