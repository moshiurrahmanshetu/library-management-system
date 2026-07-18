<?php
/**
 * Edit shelf view.
 */

$title = 'Edit Shelf';

$oldData = flash('old') ?? [];
$shelfCode = $oldData['shelf_code'] ?? $shelf['shelf_code'];
$shelfName = $oldData['shelf_name'] ?? $shelf['shelf_name'];
$floor = $oldData['floor'] ?? $shelf['floor'];
$description = $oldData['description'] ?? $shelf['description'];
$status = $oldData['status'] ?? $shelf['status'];
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-columns-gap me-2 text-primary"></i>Edit Shelf</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('shelves/update/' . $shelf['id']) ?>" method="POST" novalidate">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="shelf_code" class="form-label">Shelf code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="shelf_code" name="shelf_code" value="<?= e($shelfCode) ?>" required maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="shelf_name" class="form-label">Shelf name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="shelf_name" name="shelf_name" value="<?= e($shelfName) ?>" required maxlength="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <input type="text" class="form-control" id="floor" name="floor" value="<?= e($floor) ?>" maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?= e($description) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('shelves') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Shelf</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
