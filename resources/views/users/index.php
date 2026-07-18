<?php
/**
 * Users list view.
 */

$title = 'Users';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Users</h2>
    <?php if (can('users.create')): ?>
        <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create User
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('users') ?>" method="GET" class="row g-2">
            <div class="col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search by name or email">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
            <?php if ($search !== ''): ?>
                <div class="col-auto">
                    <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Clear</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $userRow): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?= e($userRow['name']) ?></td>
                                <td><?= e($userRow['email']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= e($userRow['role_name']) ?></span></td>
                                <td>
                                    <?php if ($userRow['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= format_datetime($userRow['created_at'], 'M j, Y') ?></td>
                                <td class="text-end pe-4">
                                    <?php if (can('users.view')): ?>
                                        <a href="<?= base_url('users/show/' . $userRow['id']) ?>" class="btn btn-sm btn-outline-info me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('users.edit')): ?>
                                        <a href="<?= base_url('users/edit/' . $userRow['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($userRow['status'] === 'active'): ?>
                                            <form action="<?= base_url('users/deactivate/' . $userRow['id']) ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate"><i class="bi bi-person-x"></i></button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= base_url('users/activate/' . $userRow['id']) ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activate"><i class="bi bi-person-check"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($lastPage > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('users?page=' . ($page - 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('users?page=' . $i . ($search ? '&search=' . urlencode($search) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $lastPage): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('users?page=' . ($page + 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
