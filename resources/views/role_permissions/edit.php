<?php
/**
 * Role permissions assignment view.
 */

$title = 'Assign Permissions';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">
        <i class="bi bi-shield-check me-2 text-primary"></i>
        Assign Permissions to <?= e($role['name']) ?>
    </h2>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">Back to Roles</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <form action="<?= base_url('role-permissions/update/' . $role['id']) ?>" method="POST" id="role-permissions-form" novalidate>
            <?= csrf_field() ?>

            <?php if (empty($groupedPermissions)): ?>
                <div class="row g-4">
                    <?php foreach ($groupedPermissions as $module => $perms): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-capitalize"><?= e($module) ?></span>
                                    <div class="form-check">
                                        <input class="form-check-input check-all-module" type="checkbox" id="check-all-<?= e($module) ?>" data-module="<?= e($module) ?>">
                                        <label class="form-check-label small" for="check-all-<?= e($module) ?>">All</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($perms as $perm): ?>
                                        <?php $isChecked = in_array((int)$perm['id'], $assignedPermissionIds, true); ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox permission-<?= e($module) ?>" type="checkbox" name="permissions[]" value="<?= (int)$perm['id'] ?>" id="perm-<?= (int)$perm['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="perm-<?= (int)$perm['id'] ?>">
                                                <?= e($perm['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No permissions available.</p>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Permissions</button>
            </div>
        </form>
    </div>
</div>
