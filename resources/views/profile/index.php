<?php
/**
 * Profile view.
 */

$title = 'Profile';
$showSidebar = true;

$oldData = flash('old') ?? [];
$name  = $oldData['name'] ?? $user['name'] ?? '';
$email = $oldData['email'] ?? $user['email'] ?? '';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>Edit Profile</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('profile') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" placeholder="John Doe" required minlength="2" maxlength="100">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" placeholder="you@example.com" required maxlength="150">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>

                <hr class="my-4">

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

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
