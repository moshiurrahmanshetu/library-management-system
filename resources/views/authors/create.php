<?php
/**
 * Create author view.
 */

$title = 'Add Author';

$oldData = flash('old') ?? [];
$fullName = $oldData['full_name'] ?? '';
$biography = $oldData['biography'] ?? '';
$status = $oldData['status'] ?? 'active';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Add Author</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('authors/store') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= e($fullName) ?>" required minlength="2" maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="biography" class="form-label">Biography</label>
                        <textarea class="form-control" id="biography" name="biography" rows="4" maxlength="1000"><?= e($biography) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('authors') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Author</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
