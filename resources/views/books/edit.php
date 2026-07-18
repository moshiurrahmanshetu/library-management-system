<?php
/**
 * Edit book view.
 */

$title = 'Edit Book';

$oldData = flash('old') ?? [];
$titleValue = $oldData['title'] ?? $book['title'];
$categoryId = $oldData['category_id'] ?? $book['category_id'];
$authorId = $oldData['author_id'] ?? $book['author_id'];
$publisherId = $oldData['publisher_id'] ?? $book['publisher_id'];
$shelfId = $oldData['shelf_id'] ?? $book['shelf_id'];
$isbn10 = $oldData['isbn10'] ?? $book['isbn10'];
$isbn13 = $oldData['isbn13'] ?? $book['isbn13'];
$edition = $oldData['edition'] ?? $book['edition'];
$language = $oldData['language'] ?? $book['language'];
$publishYear = $oldData['publish_year'] ?? $book['publish_year'];
$totalPages = $oldData['total_pages'] ?? $book['total_pages'];
$description = $oldData['description'] ?? $book['description'];
$status = $oldData['status'] ?? $book['status'];
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-book me-2 text-primary"></i>Edit Book</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('books/update/' . $book['id']) ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= e($titleValue) ?>" required maxlength="255">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= (int) $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="author_id" class="form-label">Author <span class="text-danger">*</span></label>
                            <select class="form-select" id="author_id" name="author_id" required>
                                <option value="">Select Author</option>
                                <?php foreach ($authors as $author): ?>
                                    <option value="<?= $author['id'] ?>" <?= (int) $authorId === (int) $author['id'] ? 'selected' : '' ?>>
                                        <?= e($author['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publisher_id" class="form-label">Publisher</label>
                            <select class="form-select" id="publisher_id" name="publisher_id">
                                <option value="">Select Publisher</option>
                                <?php foreach ($publishers as $publisher): ?>
                                    <option value="<?= $publisher['id'] ?>" <?= (int) $publisherId === (int) $publisher['id'] ? 'selected' : '' ?>>
                                        <?= e($publisher['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="shelf_id" class="form-label">Shelf</label>
                            <select class="form-select" id="shelf_id" name="shelf_id">
                                <option value="">Select Shelf</option>
                                <?php foreach ($shelves as $shelf): ?>
                                    <option value="<?= $shelf['id'] ?>" <?= (int) $shelfId === (int) $shelf['id'] ? 'selected' : '' ?>>
                                        <?= e($shelf['shelf_code'] . ' - ' . $shelf['shelf_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="isbn10" class="form-label">ISBN-10</label>
                            <input type="text" class="form-control" id="isbn10" name="isbn10" value="<?= e($isbn10) ?>" maxlength="20">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="isbn13" class="form-label">ISBN-13</label>
                            <input type="text" class="form-control" id="isbn13" name="isbn13" value="<?= e($isbn13) ?>" maxlength="20">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edition" class="form-label">Edition</label>
                            <input type="text" class="form-control" id="edition" name="edition" value="<?= e($edition) ?>" maxlength="50">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="language" class="form-label">Language</label>
                            <input type="text" class="form-control" id="language" name="language" value="<?= e($language) ?>" maxlength="50">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="publish_year" class="form-label">Publish Year</label>
                            <input type="number" class="form-control" id="publish_year" name="publish_year" value="<?= e($publishYear) ?>" min="1000" max="2100">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="total_pages" class="form-label">Total Pages</label>
                            <input type="number" class="form-control" id="total_pages" name="total_pages" value="<?= e($totalPages) ?>" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Allowed: jpg, jpeg, png, webp. Max size: 2MB. Leave empty to keep current cover.</div>
                        </div>
                    </div>

                    <?php if (!empty($book['cover_image'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Cover</label>
                            <div>
                                <img src="<?= e(upload_url($book['cover_image'])) ?>" alt="Current cover" class="rounded border" style="max-height: 150px;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" maxlength="2000"><?= e($description) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('books') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
