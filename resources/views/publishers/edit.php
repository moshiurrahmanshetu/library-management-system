<?php
/**
 * Edit publisher view.
 */

$title = 'Edit Publisher';
$showSidebar = true;

$oldData = flash('old') ?? [];
$name = $oldData['name'] ?? $publisher['name'];
$phone = $oldData['phone'] ?? $publisher['phone'];
$email = $oldData['email'] ?? $publisher['email'];
$website = $oldData['website'] ?? $publisher['website'];
$address = $oldData['address'] ?? $publisher['address'];
$status = $oldData['status'] ?? $publisher['status'];

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>Edit Publisher</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('publishers/update/' . $publisher['id']) ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Publisher name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" required minlength="2" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= e($phone) ?>" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" maxlength="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="website" name="website" value="<?= e($website) ?>" maxlength="255" placeholder="https://example.com">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="500"><?= e($address) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('publishers') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Publisher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
