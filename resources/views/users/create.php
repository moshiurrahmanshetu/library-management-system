<?php
/**
 * Create user view.
 */
$title = 'Create User';
$old = flash('old') ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Create User</h2>
    <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Back to Users</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <form action="<?= base_url('users/store') ?>" method="POST" novalidate>
            <?= csrf_field() ?>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required maxlength="150">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= e($old['username'] ?? '') ?>" maxlength="50">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" maxlength="20">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role) : ?>
                            <option value="<?= $role['id'] ?>" <?= ($old['role_id'] ?? 4) == $role['id'] ? 'selected' : '' ?>>
                                <?= e($role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($old['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= e($old['date_of_birth'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" maxlength="255"><?= e($old['address'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500"><?= e($old['notes'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>
