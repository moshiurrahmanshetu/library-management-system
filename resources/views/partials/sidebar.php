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
    $menuItems[] = [
        'uri' => 'role-permissions',
        'label' => 'Role Permissions',
        'icon' => 'bi-shield-check',
        'permission' => 'role_permissions.view'
    ];
}

$bookModuleUris = ['categories', 'authors', 'publishers', 'shelves', 'books'];
$isBookModuleActive = false;
foreach ($bookModuleUris as $bookUri) {
    if ($currentUri === $bookUri || str_starts_with($currentUri, $bookUri . '/')) {
        $isBookModuleActive = true;
        break;
    }
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

                $isActive =
                    $currentUri === $itemUri ||
                    str_starts_with($currentUri, $itemUri . '/');
                $activeClass = $isActive ? 'active bg-primary' : 'text-white';
                $href = base_url($itemUri); // Role Permissions doesn't have an index page, so we'll link to #!
            ?>
            <a href="<?= $href ?>" class="nav-link rounded mb-2 <?= $activeClass ?>">
                <i class="bi <?= $item['icon'] ?> me-2"></i>
                <?= e($item['label']) ?>
            </a>
        <?php endforeach; ?>

        <?php if (can('books.view')): ?>
            <div class="nav-item mb-2">
                <a class="nav-link rounded d-flex justify-content-between align-items-center <?= $isBookModuleActive ? 'active bg-primary' : 'text-white' ?>"
                   href="#" data-bs-toggle="collapse" data-bs-target="#booksMenu" aria-expanded="<?= $isBookModuleActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-book me-2"></i>Books</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isBookModuleActive ? 'show' : '' ?>" id="booksMenu">
                    <div class="ps-4 pt-1">
                        <a href="<?= base_url('categories') ?>" class="nav-link rounded py-1 <?= $currentUri === 'categories' || str_starts_with($currentUri, 'categories/') ? 'active bg-primary' : 'text-white' ?>">
                            Categories
                        </a>
                        <a href="<?= base_url('authors') ?>" class="nav-link rounded py-1 <?= $currentUri === 'authors' || str_starts_with($currentUri, 'authors/') ? 'active bg-primary' : 'text-white' ?>">
                            Authors
                        </a>
                        <a href="<?= base_url('publishers') ?>" class="nav-link rounded py-1 <?= $currentUri === 'publishers' || str_starts_with($currentUri, 'publishers/') ? 'active bg-primary' : 'text-white' ?>">
                            Publishers
                        </a>
                        <a href="<?= base_url('shelves') ?>" class="nav-link rounded py-1 <?= $currentUri === 'shelves' || str_starts_with($currentUri, 'shelves/') ? 'active bg-primary' : 'text-white' ?>">
                            Shelves
                        </a>
                        <a href="<?= base_url('books') ?>" class="nav-link rounded py-1 <?= $currentUri === 'books' || str_starts_with($currentUri, 'books/') ? 'active bg-primary' : 'text-white' ?>">
                            Books
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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
