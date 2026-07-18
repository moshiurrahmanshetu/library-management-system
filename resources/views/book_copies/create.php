<?php
/**
 * Create book copy view.
 */

$title = 'Add Copy - ' . $book['title'];
$showSidebar = true;

$oldData = flash('old') ?? [];
$accessionNumber = $oldData['accession_number'] ?? '';
$barcode = $oldData['barcode'] ?? '';
$purchaseDate = $oldData['purchase_date'] ?? '';
$purchasePrice = $oldData['purchase_price'] ?? '';
$condition = $oldData['book_condition'] ?? 'good';
$status = $oldData['status'] ?? 'available';
$notes = $oldData['notes'] ?? '';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-copy me-2 text-primary"></i>Add Book Copy</h5>
                <p class="text-muted mb-0 small"><?= e($book['title']) ?></p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('books/' . $book['id'] . '/copies/store') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="accession_number" class="form-label">Accession Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="accession_number" name="accession_number" value="<?= e($accessionNumber) ?>" required maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="barcode" name="barcode" value="<?= e($barcode) ?>" maxlength="100">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= e($purchaseDate) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="<?= e($purchasePrice) ?>" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select" id="condition" name="condition">
                                <option value="new" <?= $condition === 'new' ? 'selected' : '' ?>>New</option>
                                <option value="good" <?= $condition === 'good' ? 'selected' : '' ?>>Good</option>
                                <option value="fair" <?= $condition === 'fair' ? 'selected' : '' ?>>Fair</option>
                                <option value="poor" <?= $condition === 'poor' ? 'selected' : '' ?>>Poor</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?= $status === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="lost" <?= $status === 'lost' ? 'selected' : '' ?>>Lost</option>
                                <option value="damaged" <?= $status === 'damaged' ? 'selected' : '' ?>>Damaged</option>
                                <option value="withdrawn" <?= $status === 'withdrawn' ? 'selected' : '' ?>>Withdrawn</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500"><?= e($notes) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('books/' . $book['id'] . '/copies') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Copy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/main.php';
