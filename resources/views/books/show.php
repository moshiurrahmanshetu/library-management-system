<?php
/**
 * Book details view.
 */

$title = e($book['title']);
$showSidebar = true;

$statusBadge = $book['status'] === 'active'
    ? '<span class="badge bg-success">Active</span>'
    : '<span class="badge bg-secondary">Inactive</span>';

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Book Details</h2>
    <div>
        <a href="<?= base_url('books') ?>" class="btn btn-outline-secondary btn-sm">Back to Books</a>
        <?php if (can('books.edit')): ?>
            <a href="<?= base_url('books/edit/' . $book['id']) ?>" class="btn btn-primary btn-sm">Edit Book</a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <?php if (!empty($book['cover_image'])): ?>
                            <img src="<?= e(upload_url($book['cover_image'])) ?>" alt="Cover" class="rounded border img-fluid" style="max-height: 320px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" style="height: 320px;">
                                <i class="bi bi-book fs-1 text-secondary"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h3 class="h4"><?= e($book['title']) ?> <?= $statusBadge ?></h3>
                        <p class="text-muted mb-3"><?= e($book['category_name']) ?></p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Author</strong>
                                <?= e($book['author_name']) ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Publisher</strong>
                                <?= e($book['publisher_name']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">ISBN-10</strong>
                                <?= e($book['isbn10']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">ISBN-13</strong>
                                <?= e($book['isbn13']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Edition</strong>
                                <?= e($book['edition']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Language</strong>
                                <?= e($book['language']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Publish Year</strong>
                                <?= e($book['publish_year']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Total Pages</strong>
                                <?= e($book['total_pages']) ?: 'N/A' ?>
                            </div>
                            <div class="col-sm-6">
                                <strong class="d-block text-muted small">Shelf</strong>
                                <?php if ($book['shelf_code']): ?>
                                    <?= e($book['shelf_code'] . ' - ' . $book['shelf_name']) ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($book['description'])): ?>
                            <div class="mt-4">
                                <strong class="d-block text-muted small mb-1">Description</strong>
                                <p class="mb-0"><?= nl2br(e($book['description'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-collection me-2 text-primary"></i>Copy Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Copies</span>
                    <span class="fs-4 fw-bold"><?= (int) $copySummary['total'] ?></span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Available
                        <span class="badge bg-success rounded-pill"><?= (int) $copySummary['available'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Lost
                        <span class="badge bg-dark rounded-pill"><?= (int) $copySummary['lost'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Damaged
                        <span class="badge bg-warning text-dark rounded-pill"><?= (int) $copySummary['damaged'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Withdrawn
                        <span class="badge bg-secondary rounded-pill"><?= (int) $copySummary['withdrawn'] ?></span>
                    </li>
                </ul>
                <div class="d-grid mt-3">
                    <a href="<?= base_url('books/' . $book['id'] . '/copies') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i> Manage Copies
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Copies</h5>
        <?php if (can('books.edit')): ?>
            <a href="<?= base_url('books/' . $book['id'] . '/copies/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Copy
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Accession #</th>
                        <th>Barcode</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentCopies)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No copies found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentCopies as $copy): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?= e($copy['accession_number']) ?></td>
                                <td class="text-muted"><?= e($copy['barcode']) ?: 'N/A' ?></td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= ucfirst(e($copy['book_condition'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $copyStatusClass = match ($copy['status']) {
                                        'available' => 'bg-success',
                                        'lost' => 'bg-dark',
                                        'damaged' => 'bg-warning text-dark',
                                        'withdrawn' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $copyStatusClass ?>"><?= ucfirst(e($copy['status'])) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (can('books.edit')): ?>
                                        <a href="<?= base_url('books/' . $book['id'] . '/copies/edit/' . $copy['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
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

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
