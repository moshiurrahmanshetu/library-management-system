<?php
/**
 * Dynamic sidebar partial for authenticated pages.
 *
 * Menu items are only shown when the user has the corresponding permission.
 */

$currentUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

$menuItems = [];

if (can('dashboard.view')) {
    $menuItems[] = ['uri' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'permission' => 'dashboard.view'];
}

if (can('users.view')) {
    $menuItems[] = ['uri' => 'users', 'label' => 'Users', 'icon' => 'bi-people', 'permission' => 'users.view'];
}

if (can('roles.view')) {
    $menuItems[] = ['uri' => 'roles', 'label' => 'Roles', 'icon' => 'bi-person-gear', 'permission' => 'roles.view'];
}

if (can('permissions.view')) {
    $menuItems[] = ['uri' => 'permissions', 'label' => 'Permissions', 'icon' => 'bi-shield-lock', 'permission' => 'permissions.view'];
}

if (can('role_permissions.view')) {
    $menuItems[] = ['uri' => 'roles', 'label' => 'Role Permissions', 'icon' => 'bi-shield-check', 'permission' => 'role_permissions.view'];
}

// Always-visible account menu items.
$menuItems[] = ['uri' => 'profile', 'label' => 'Profile', 'icon' => 'bi-person', 'permission' => null];
$menuItems[] = ['uri' => 'password/change', 'label' => 'Change Password', 'icon' => 'bi-shield-lock', 'permission' => null];

?>

<aside class="bg-dark text-white vh-100" id="sidebar" style="min-width: 260px; max-width: 260px;">
    <div class="d-flex align-items-center justify-content-center py-4 border-bottom border-secondary">
        <i class="bi bi-book-half fs-3 me-2"></i>
        <span class="fw-bold fs-5">Library MS</span>
    </div>

    <nav class="nav flex-column px-3 py-3">
        <?php foreach ($menuItems as $item): ?>
            <?php
                $itemUri = $item['uri'];
                $isActive = $currentUri === $itemUri || str_starts_with($currentUri, $itemUri . '/');
                $activeClass = $isActive ? 'active bg-primary' : 'text-white';
            ?>
            <a href="<?= base_url($itemUri) ?>" class="nav-link rounded mb-2 <?= $activeClass ?>">
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
