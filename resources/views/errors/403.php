<?php
/**
 * 403 forbidden view.
 */

$title = 'Access Denied';

ob_start();
?>

<div class="text-center px-3">
    <h1 class="display-1 fw-bold text-danger">403</h1>
    <p class="fs-4">Access Denied</p>
    <p class="text-muted mb-4">You do not have permission to view this page.</p>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">Return to Dashboard</a>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
