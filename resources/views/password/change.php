<?php
/**
 * Change password view.
 */

$title = 'Change Password';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Change Password</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('password/change') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your current password" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="At least 8 characters" required minlength="8">
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm new password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat your new password" required minlength="8">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
