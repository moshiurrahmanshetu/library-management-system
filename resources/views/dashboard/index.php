<?php
/**
 * Dashboard view.
 */

$title = 'Dashboard';
$showSidebar = true;

ob_start();
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h4 card-title mb-1">Welcome, <?= e($user['name']) ?>!</h2>
                <p class="text-muted mb-0">
                    You are logged in as <span class="badge bg-primary"><?= e($user['role_name']) ?></span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person-badge me-2 text-primary"></i>Logged in User</h5>
                <p class="card-text">
                    <strong>Name:</strong> <?= e($user['name']) ?><br>
                    <strong>Email:</strong> <?= e($user['email']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-shield-check me-2 text-success"></i>Current Role</h5>
                <p class="card-text">
                    <span class="fs-4 fw-bold"><?= e($user['role_name']) ?></span><br>
                    <span class="text-muted small">Role ID: <?= e($user['role_id']) ?></span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person-lines-fill me-2 text-info"></i>Profile Completion</h5>
                <div class="progress" role="progressbar" aria-label="Profile completion" aria-valuenow="<?= (int) $profileCompletion ?>" aria-valuemin="0" aria-valuemax="100" style="height: 24px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?= (int) $profileCompletion ?>%"><?= (int) $profileCompletion ?>%</div>
                </div>
                <p class="text-muted small mt-2 mb-0">Complete your profile to unlock all features.</p>
            </div>
        </div>
    </div>

    <?php if ($canViewUsers || $canViewBooks || $canViewReports || $canViewSettings): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-graph-up me-2"></i>Quick Statistics
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if ($canViewUsers): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Registered Users</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($canViewBooks): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Total Books</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Borrowed Books</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Overdue Books</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($canViewReports): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Reports</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($canViewSettings): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="text-muted small">Settings</div>
                                    <div class="fs-3 fw-bold text-secondary">-</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
