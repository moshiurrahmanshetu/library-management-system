<?php
/**
 * Login view.
 */

$title = 'Login';
$email = old('email');

ob_start();
?>

<div class="card shadow-sm" style="width: 100%; max-width: 420px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="bi bi-book-half fs-1 text-primary"></i>
            <h1 class="h4 mt-2">Welcome back</h1>
            <p class="text-muted small">Sign in to your library account</p>
        </div>

        <form action="<?= base_url('login') ?>" method="POST" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" placeholder="you@example.com" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign In</button>
            </div>
        </form>

        <hr class="my-4">

        <p class="text-center text-muted small mb-0">
            Don't have an account? <a href="<?= base_url('register') ?>" class="text-decoration-none">Register</a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
