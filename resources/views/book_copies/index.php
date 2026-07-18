<?php
/**
 * Book copies list view.
 */

$title = 'Copies of ' . $book['title'];
$showSidebar = true;

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">Book Copies</h2>
        <p class="text-muted mb-0"><?= e($book['title']) ?></p>
    </div>
    <div>
        <a href="<?= base_url('books/show/' . $book['id']) ?>" class="btn btn-outline-secondary btn-sm me-2">Back to Book</a>
        <?php if (can('books.edit')): ?>
            <a href="<?= base_url('books/' . $book['id'] . '/copies/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Copy
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Accession #</th>
                        <th>Barcode</th>
                        <th>Purchase Date</th>
                        <th>Price</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($copies)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No copies found for this book.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($copies as $copy): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?= e($copy['accession_number']) ?></td>
                                <td class="text-muted"><?= e($copy['barcode']) ?: 'N/A' ?></td>
                                <td><?= e($copy['purchase_date']) ?: 'N/A' ?></td>
                                <td><?= $copy['purchase_price'] ? number_format((float) $copy['purchase_price'], 2) : 'N/A' ?></td>
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
                                        <a href="<?= base_url('books/' . $book['id'] . '/copies/edit/' . $copy['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('books.delete')): ?>
                                        <form action="<?= base_url('books/' . $book['id'] . '/copies/delete/' . $copy['id']) ?>" method="POST" class="d-inline delete-form">
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
    <nav aria-label="Copies pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('books/' . $book['id'] . '/copies?page=' . ($page - 1)) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('books/' . $book['id'] . '/copies?page=' . $i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $lastPage): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url('books/' . $book['id'] . '/copies?page=' . ($page + 1)) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
