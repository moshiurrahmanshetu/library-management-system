<?php
/**
 * User detail view.
 */

$title = 'User Details';
$showSidebar = true;

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>User Details</h5>
                <a href="<?= base_url('users') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="card-body p-4 p-md-5">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th style="width: 180px;">Name</th>
                            <td><?= e($user['name']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= e($user['email']) ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><span class="badge bg-info text-dark"><?= e($user['role_name']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Member Since</th>
                            <td><?= format_datetime($user['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Last Login</th>
                            <td><?= format_datetime($user['last_login_at']) ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php if (can('users.edit')): ?>
                    <div class="mt-3">
                        <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-primary">Edit User</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
