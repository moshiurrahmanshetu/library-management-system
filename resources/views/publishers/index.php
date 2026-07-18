<?php
/**
 * Publishers list view.
 */

$title = 'Publishers';
$showSidebar = true;

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Publishers</h2>
    <?php if (can('books.create')): ?>
        <a href="<?= base_url('publishers/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Publisher
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('publishers') ?>" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" value="<?= e($search) ?>" placeholder="Search by name, email or address">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                    <?php if ($search): ?>
                        <a href="<?= base_url('publishers') ?>" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </div>
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
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($publishers)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No publishers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($publishers as $publisher): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?= e($publisher['name']) ?></td>
                                <td class="text-muted"><?= e($publisher['email']) ?></td>
                                <td><?= e($publisher['phone']) ?></td>
                                <td>
                                    <?php if ($publisher['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (can('books.edit')): ?>
                                        <a href="<?= base_url('publishers/edit/' . $publisher['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('books.delete')): ?>
                                        <form action="<?= base_url('publishers/delete/' . $publisher['id']) ?>" method="POST" class="d-inline delete-form">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
    <nav aria-label="Publishers pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('publishers?page=' . ($page - 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('publishers?page=' . $i . ($search ? '&search=' . urlencode($search) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $lastPage): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('publishers?page=' . ($page + 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
