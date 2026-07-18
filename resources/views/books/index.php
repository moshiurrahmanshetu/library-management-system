<?php
/**
 * Books list view.
 */

$title = 'Books';

$filterSearch = $filters['search'] ?? '';
$filterCategoryId = $filters['category_id'] ?? null;
$filterAuthorId = $filters['author_id'] ?? null;
$filterPublisherId = $filters['publisher_id'] ?? null;
$filterShelfId = $filters['shelf_id'] ?? null;
$filterStatus = $filters['status'] ?? null;

$queryParams = [];
if ($filterSearch) {
    $queryParams['search'] = $filterSearch;
}
if ($filterCategoryId) {
    $queryParams['category_id'] = $filterCategoryId;
}
if ($filterAuthorId) {
    $queryParams['author_id'] = $filterAuthorId;
}
if ($filterPublisherId) {
    $queryParams['publisher_id'] = $filterPublisherId;
}
if ($filterShelfId) {
    $queryParams['shelf_id'] = $filterShelfId;
}
if ($filterStatus) {
    $queryParams['status'] = $filterStatus;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Books</h2>
    <?php if (can('books.create')): ?>
        <a href="<?= base_url('books/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Book
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('books') ?>" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" value="<?= e($filterSearch) ?>" placeholder="Search title, ISBN or author">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (int)$filterCategoryId === (int)$category['id'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="author_id" class="form-select">
                        <option value="">All Authors</option>
                        <?php foreach ($authors ?? [] as $author): ?>
                            <option value="<?= $author['id'] ?>" <?= (int)$filterAuthorId === (int)$author['id'] ? 'selected' : '' ?>>
                                <?= e($author['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="publisher_id" class="form-select">
                        <option value="">All Publishers</option>
                        <?php foreach ($publishers ?? [] as $publisher): ?>
                            <option value="<?= $publisher['id'] ?>" <?= (int)$filterPublisherId === (int)$publisher['id'] ? 'selected' : '' ?>>
                                <?= e($publisher['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="<?= base_url('books') ?>" class="btn btn-outline-secondary">Clear</a>
                    </div>
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
                        <th class="ps-4">Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>ISBN-13</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No books found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if (!empty($book['cover_image'])): ?>
                                        <img src="<?= e(upload_url($book['cover_image'])) ?>" alt="Cover" class="rounded" style="width: 48px; height: 64px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 64px;">
                                            <i class="bi bi-book text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-medium"><?= e($book['title']) ?></td>
                                <td><?= e($book['category_name']) ?></td>
                                <td><?= e($book['author_name']) ?></td>
                                <td class="text-muted"><?= e($book['publisher_name']) ?></td>
                                <td class="text-muted"><?= e($book['isbn13']) ?></td>
                                <td>
                                    <?php if ($book['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('books/show/' . $book['id']) ?>" class="btn btn-sm btn-outline-info me-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (can('books.edit')): ?>
                                        <a href="<?= base_url('books/edit/' . $book['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('books.delete')): ?>
                                        <form action="<?= base_url('books/delete/' . $book['id']) ?>" method="POST" class="d-inline delete-form">
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
    <nav aria-label="Books pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if (($page ?? 1) > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('books?' . http_build_query(array_merge($queryParams, ['page' => ($page ?? 1) - 1]))) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= ($lastPage ?? 1); $i++): ?>
                <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('books?' . http_build_query(array_merge($queryParams, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if (($page ?? 1) < ($lastPage ?? 1)): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('books?' . http_build_query(array_merge($queryParams, ['page' => ($page ?? 1) + 1]))) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
