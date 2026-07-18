<?php
/**
 * Registration view.
 */

$title = 'Register';
$oldData = flash('old') ?? [];
$name  = $oldData['name'] ?? '';
$email = $oldData['email'] ?? '';
?>

<div class="card shadow-sm" style="width: 100%; max-width: 460px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="bi bi-book-half fs-1 text-primary"></i>
            <h1 class="h4 mt-2">Create account</h1>
            <p class="text-muted small">Register to access the library system</p>
        </div>

        <form action="<?= base_url('register') ?>" method="POST" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Full name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= e($name) ?>" placeholder="John Doe" required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" placeholder="you@example.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="At least 8 characters" required minlength="8">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required minlength="8">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>

        <hr class="my-4">

        <p class="text-center text-muted small mb-0">
            Already have an account? <a href="<?= base_url('login') ?>" class="text-decoration-none">Sign in</a>
        </p>
    </div>
</div>
