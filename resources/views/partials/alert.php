<?php
/**
 * Flash alert partial.
 *
 * Renders success, error and validation messages stored in the session.
 */

$success = flash('success');
$error = flash('error');
$errors = flash('errors') ?? [];
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= e($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-circle-fill me-2"></i>Please fix the following issues:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $fieldError): ?>
                <li><?= e($fieldError) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
