<?php
/**
 * 404 error view.
 */

$title = 'Page Not Found';

ob_start();
?>

<div class="text-center">
    <h1 class="display-1 fw-bold text-muted">404</h1>
    <p class="fs-4">Page not found</p>
    <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
    <a href="<?= base_url() ?>" class="btn btn-primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
