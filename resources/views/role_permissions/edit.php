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
    <a href="<?= base_url('role-permissions') ?>" class="btn btn-outline-secondary">Back to Role Permissions</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <!-- Search and Actions -->
        <div class="d-flex flex-wrap gap-3 mb-4">
            <div class="flex-grow-1">
                <input type="text" id="permission-search" class="form-control" placeholder="Search permissions...">
            </div>
            <div>
                <button type="button" id="btn-select-all" class="btn btn-outline-primary me-2">
                    <i class="bi bi-check-all me-1"></i> Select All
                </button>
                <button type="button" id="btn-clear-all" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> Clear All
                </button>
            </div>
        </div>

        <form action="<?= base_url('role-permissions/update/' . $role['id']) ?>" method="POST" id="role-permissions-form" novalidate>
            <?= csrf_field() ?>

            <?php if (!empty($groupedPermissions)): ?>
                <div class="row g-4" id="permission-groups">
                    <?php foreach ($groupedPermissions as $module => $perms): ?>
                        <div class="col-md-6 col-lg-4 permission-module" data-module="<?= e($module) ?>">
                            <div class="card h-100 border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-capitalize"><?= e($module) ?></span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary select-module" data-module="<?= e($module) ?>">
                                            <i class="bi bi-check"></i> All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary clear-module" data-module="<?= e($module) ?>">
                                            <i class="bi bi-x"></i> Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($perms as $perm): ?>
                                        <?php $isChecked = in_array((int)$perm['id'], $assignedPermissionIds, true); ?>
                                        <div class="form-check mb-2 permission-item" data-name="<?= e(strtolower($perm['name'])) ?>">
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
                <a href="<?= base_url('role-permissions') ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Permissions</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('permission-search');
    const selectAllBtn = document.getElementById('btn-select-all');
    const clearAllBtn = document.getElementById('btn-clear-all');
    const permissionGroups = document.getElementById('permission-groups');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const modules = permissionGroups.querySelectorAll('.permission-module');
        
        modules.forEach(module => {
            const items = module.querySelectorAll('.permission-item');
            let hasVisible = false;
            
            items.forEach(item => {
                const name = item.dataset.name;
                if (name.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (searchTerm === '') {
                module.style.display = 'block';
            } else {
                module.style.display = hasVisible ? 'block' : 'none';
            }
        });
    });

    // Select All
    selectAllBtn.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');
        checkboxes.forEach(cb => cb.checked = true);
    });

    // Clear All
    clearAllBtn.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');
        checkboxes.forEach(cb => cb.checked = false);
    });

    // Module-wise select all
    document.querySelectorAll('.select-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`.permission-${module}:not(:disabled)`);
            checkboxes.forEach(cb => cb.checked = true);
        });
    });

    // Module-wise clear all
    document.querySelectorAll('.clear-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`.permission-${module}:not(:disabled)`);
            checkboxes.forEach(cb => cb.checked = false);
        });
    });

    // Disable all checkboxes for Super Admin (ID 1)
    <?php if ($role['id'] === 1): ?>
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.disabled = true;
    });
    <?php endif; ?>
});
</script>

