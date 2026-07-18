<?php
/**
 * Permissions list view.
 */

$title = 'Permissions';
$showSidebar = true;

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Permissions</h2>
    <?php if (can('permissions.create')): ?>
        <a href="<?= base_url('permissions/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Permission
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permissions)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No permissions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><code><?= e($permission['name']) ?></code></td>
                                <td class="text-muted"><?= e($permission['description']) ?></td>
                                <td><?= format_datetime($permission['created_at'], 'M j, Y') ?></td>
                                <td class="text-end pe-4">
                                    <?php if (can('permissions.edit')): ?>
                                        <a href="<?= base_url('permissions/edit/' . $permission['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('permissions.delete')): ?>
                                        <form action="<?= base_url('permissions/delete/' . $permission['id']) ?>" method="POST" class="d-inline delete-form">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
