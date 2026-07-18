<?php
/**
 * Authors list view.
 */

$title = 'Authors';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Authors</h2>
    <?php if (can('books.create')): ?>
        <a href="<?= base_url('authors/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Author
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('authors') ?>" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" value="<?= e($search ?? '') ?>" placeholder="Search by name or biography">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                    <?php if (($search ?? '') !== ''): ?>
                        <a href="<?= base_url('authors') ?>" class="btn btn-outline-secondary">Clear</a>
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
                        <th class="ps-4">Full Name</th>
                        <th>Biography</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($authors)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No authors found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($authors as $author): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?= e($author['full_name']) ?></td>
                                <td class="text-muted"><?= e($author['biography']) ?></td>
                                <td>
                                    <?php if ($author['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (can('books.edit')): ?>
                                        <a href="<?= base_url('authors/edit/' . $author['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('books.delete')): ?>
                                        <form action="<?= base_url('authors/delete/' . $author['id']) ?>" method="POST" class="d-inline delete-form">
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

<?php if (($lastPage ?? 1) > 1): ?>
    <nav aria-label="Authors pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if (($page ?? 1) > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('authors?page=' . (($page ?? 1) - 1) . (($search ?? '') ? '&search=' . urlencode($search) : '')) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= ($lastPage ?? 1); $i++): ?>
                <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('authors?page=' . $i . (($search ?? '') ? '&search=' . urlencode($search) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if (($page ?? 1) < ($lastPage ?? 1)): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('authors?page=' . (($page ?? 1) + 1) . (($search ?? '') ? '&search=' . urlencode($search) : '')) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
