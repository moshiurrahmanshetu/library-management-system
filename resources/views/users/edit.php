<?php
/**
 * Admin edit user view.
 */

$title = 'Edit User';

$oldData = flash('old') ?? [];
$name = $oldData['name'] ?? $user['name'];
$email = $oldData['email'] ?? $user['email'];
$username = $oldData['username'] ?? ($user['username'] ?? '');
$phone = $oldData['phone'] ?? ($user['phone'] ?? '');
$roleId = $oldData['role_id'] ?? $user['role_id'];
$status = $oldData['status'] ?? $user['status'];
$gender = $oldData['gender'] ?? ($user['gender'] ?? '');
$dateOfBirth = $oldData['date_of_birth'] ?? ($user['date_of_birth'] ?? '');
$address = $oldData['address'] ?? ($user['address'] ?? '');
$notes = $oldData['notes'] ?? ($user['notes'] ?? '');

$canChangeRole = ($currentUserRoleId ?? 0) == 1;
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-person-gear me-2 text-primary"></i>Edit User
            </h2>
            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Back to Users</a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('users/update/' . $user['id']) ?>" method="POST" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" required minlength="2" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" required maxlength="150">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= e($username) ?>" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= e($phone) ?>" maxlength="20">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select" id="role_id" name="role_id" <?= !$canChangeRole ? 'disabled' : '' ?>>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= (int)$role['id'] ?>" <?= (int)$roleId === (int)$role['id'] ? 'selected' : '' ?>>
                                        <?= e($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!$canChangeRole): ?>
                                <input type="hidden" name="role_id" value="<?= (int)$roleId ?>">
                                <div class="form-text text-muted">Only Super Admin can change roles.</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= e($dateOfBirth) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="255"><?= e($address) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500"><?= e($notes) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Reset Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('users/reset-password/' . $user['id']) ?>" method="POST" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="reset-password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="reset-password" name="password" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label for="reset-password-confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="reset-password-confirmation" name="password_confirmation" required minlength="8">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Danger Zone</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Deleting this user is a permanent action and cannot be undone.
                </div>
                <div class="mt-3">
                    <form action="<?= base_url('users/delete/' . $user['id']) ?>" method="POST" class="d-inline delete-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
