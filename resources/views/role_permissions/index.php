<?php
/**
 * Role permissions index view.
 */

$title = 'Role Permissions';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Role Permissions</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Role</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No roles found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td class="fw-medium">
                                    <?php if ($role['id'] === 1): ?>
                                        <i class="bi bi-shield-star text-warning me-2"></i>
                                    <?php endif; ?>
                                    <?php echo e($role['name']); ?>
                                </td>
                                <td class="text-muted"><?php echo e($role['description'] ?? 'No description'); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo base_url('role-permissions/edit/' . $role['id']); ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil me-1"></i> Manage Permissions
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
