<?php
/**
 * Admin edit user view.
 */

$title = 'Edit User';
$showSidebar = true;

$oldData = flash('old') ?? [];
$name = $oldData['name'] ?? $user['name'];
$email = $oldData['email'] ?? $user['email'];
$roleId = $oldData['role_id'] ?? $user['role_id'];
$status = $oldData['status'] ?? $user['status'];

$canChangeRole = can('roles.view');

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2 text-primary"></i>Edit User</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('users/update/' . $user['id']) ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" required minlength="2" maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" <?= !$canChangeRole ? 'disabled' : '' ?>>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= (int) $role['id'] ?>" <?= (int) $roleId === (int) $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!$canChangeRole): ?>
                            <input type="hidden" name="role_id" value="<?= (int) $roleId ?>">
                            <div class="form-text text-muted">Only Super Admin can change roles.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
