<?php
/**
 * Profile view.
 */

$title = 'Profile';

$oldData = flash('old') ?? [];
$name = $oldData['name'] ?? $user['name'] ?? '';
$email = $oldData['email'] ?? $user['email'] ?? '';
$username = $oldData['username'] ?? ($user['username'] ?? '');
$phone = $oldData['phone'] ?? ($user['phone'] ?? '');
$gender = $oldData['gender'] ?? ($user['gender'] ?? '');
$dateOfBirth = $oldData['date_of_birth'] ?? ($user['date_of_birth'] ?? '');
$address = $oldData['address'] ?? ($user['address'] ?? '');
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>Edit Profile</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('profile') ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="mb-4 text-center">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?= e(upload_url($user['profile_photo'])) ?>" alt="Profile" class="rounded-circle border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary-subtle border d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label for="profile_photo" class="form-label fw-semibold">Profile Photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text text-muted">Allowed types: JPG, PNG, WEBP. Max size: 2MB.</div>
                        </div>
                    </div>

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
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= e($phone) ?>" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= e($dateOfBirth) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="255"><?= e($address) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row text-muted small">
                    <div class="col-md-6 mb-2">
                        <strong>Member since:</strong> <?= format_datetime($user['created_at'] ?? null, 'M j, Y') ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Last login:</strong> <?= format_datetime($user['last_login_at'] ?? null) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
