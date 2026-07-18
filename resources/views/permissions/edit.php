<?php
/**
 * Edit permission view.
 */

$title = 'Edit Permission';

$oldData = flash('old') ?? [];
$name = $oldData['name'] ?? $permission['name'];
$description = $oldData['description'] ?? $permission['description'];
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Edit Permission</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('permissions/update/' . $permission['id']) ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Permission name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" required maxlength="100">
                        <div class="form-text">Use lowercase dotted notation: <code>module.action</code></div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= e($description) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('permissions') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
