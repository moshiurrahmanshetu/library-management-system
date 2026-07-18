<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Book;
use App\Models\BookCopy;

/**
 * Book copy controller.
 *
 * Handles CRUD for individual book copies.
 */
class BookCopyController extends Controller
{
    private const PER_PAGE = 10;
    private const VALID_CONDITIONS = ['new', 'good', 'fair', 'poor'];
    private const VALID_STATUSES = ['available', 'lost', 'damaged', 'withdrawn'];

    /**
     * List copies for a book.
     *
     * @param int $bookId
     * @return void
     */
    public function index(int $bookId): void
    {
        $this->requireAuth();
        $this->authorize('books.view');

        $book = (new Book())->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        if ($page < 1) {
            $page = 1;
        }

        $copyModel = new BookCopy();
        $result = $copyModel->paginateByBook($bookId, $page, self::PER_PAGE);

        $this->view('book_copies.index', [
            'book'     => $book,
            'copies'   => $result['data'],
            'total'    => $result['total'],
            'page'     => $result['page'],
            'perPage'  => $result['per_page'],
            'lastPage' => $result['last_page'],
        ]);
    }

    /**
     * Show create copy form.
     *
     * @param int $bookId
     * @return void
     */
    public function create(int $bookId): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $book = (new Book())->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $this->view('book_copies.create', ['book' => $book]);
    }

    /**
     * Store a new book copy.
     *
     * @param int $bookId
     * @return void
     */
    public function store(int $bookId): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $bookModel = new Book();
        $book = $bookModel->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $data = $this->getCopyInput($bookId);
        $errors = $this->validateCopy($data);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/books/' . $bookId . '/copies/create');
        }

        $copyModel = new BookCopy();
        $copyModel->create($data);

        Session::setFlash('success', 'Book copy added successfully.');
        $this->redirect('/books/' . $bookId . '/copies');
    }

    /**
     * Show edit copy form.
     *
     * @param int $bookId
     * @param int $id
     * @return void
     */
    public function edit(int $bookId, int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $book = (new Book())->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $copyModel = new BookCopy();
        $copy = $copyModel->find($id);

        if (!$copy || (int) $copy['book_id'] !== $bookId) {
            Session::setFlash('error', 'Book copy not found.');
            $this->redirect('/books/' . $bookId . '/copies');
        }

        $this->view('book_copies.edit', [
            'book' => $book,
            'copy' => $copy,
        ]);
    }

    /**
     * Update a book copy.
     *
     * @param int $bookId
     * @param int $id
     * @return void
     */
    public function update(int $bookId, int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $book = (new Book())->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $copyModel = new BookCopy();
        $copy = $copyModel->find($id);

        if (!$copy || (int) $copy['book_id'] !== $bookId) {
            Session::setFlash('error', 'Book copy not found.');
            $this->redirect('/books/' . $bookId . '/copies');
        }

        $data = $this->getCopyInput($bookId);
        $errors = $this->validateCopy($data, $id);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/books/' . $bookId . '/copies/edit/' . $id);
        }

        $copyModel->update($id, $data);

        Session::setFlash('success', 'Book copy updated successfully.');
        $this->redirect('/books/' . $bookId . '/copies');
    }

    /**
     * Delete a book copy.
     *
     * @param int $bookId
     * @param int $id
     * @return void
     */
    public function destroy(int $bookId, int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $book = (new Book())->findWithRelations($bookId);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $copyModel = new BookCopy();
        $copy = $copyModel->find($id);

        if (!$copy || (int) $copy['book_id'] !== $bookId) {
            Session::setFlash('error', 'Book copy not found.');
            $this->redirect('/books/' . $bookId . '/copies');
        }

        $copyModel->delete($id);
        Session::setFlash('success', 'Book copy deleted successfully.');
        $this->redirect('/books/' . $bookId . '/copies');
    }

    /**
     * Get and sanitize copy input.
     *
     * @param int $bookId
     * @return array
     */
    private function getCopyInput(int $bookId): array
    {
        return [
            'book_id'          => $bookId,
            'accession_number' => sanitize_input($_POST['accession_number'] ?? ''),
            'barcode'          => sanitize_input($_POST['barcode'] ?? ''),
            'purchase_date'    => sanitize_input($_POST['purchase_date'] ?? ''),
            'purchase_price'   => filter_input(INPUT_POST, 'purchase_price', FILTER_VALIDATE_FLOAT) ?: null,
            'book_condition'   => sanitize_input($_POST['condition'] ?? 'good'),
            'status'           => sanitize_input($_POST['status'] ?? 'available'),
            'notes'            => sanitize_input($_POST['notes'] ?? ''),
        ];
    }

    /**
     * Validate copy input.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validateCopy(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        $copyModel = new BookCopy();

        if (trim($data['accession_number']) === '') {
            $errors['accession_number'] = 'Accession number is required.';
        } elseif (strlen($data['accession_number']) > 100) {
            $errors['accession_number'] = 'Accession number must not exceed 100 characters.';
        } elseif ($copyModel->existsByAccession($data['accession_number'], $excludeId)) {
            $errors['accession_number'] = 'A copy with this accession number already exists.';
        }

        if (!empty($data['barcode']) && strlen($data['barcode']) > 100) {
            $errors['barcode'] = 'Barcode must not exceed 100 characters.';
        } elseif (!empty($data['barcode']) && $copyModel->existsByBarcode($data['barcode'], $excludeId)) {
            $errors['barcode'] = 'A copy with this barcode already exists.';
        }

        if (!empty($data['purchase_price']) && $data['purchase_price'] < 0) {
            $errors['purchase_price'] = 'Purchase price must be a positive number.';
        }

        if (!in_array($data['book_condition'], self::VALID_CONDITIONS, true)) {
            $errors['condition'] = 'Please select a valid condition.';
        }

        if (!in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = 'Please select a valid status.';
        }

        return $errors;
    }
}
