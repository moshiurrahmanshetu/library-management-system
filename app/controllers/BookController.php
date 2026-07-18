<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Shelf;

/**
 * Book controller.
 *
 * Handles book catalog CRUD, search/filter/pagination and cover uploads.
 */
class BookController extends Controller
{
    private const PER_PAGE = 10;
    private const COVER_MAX_BYTES = 2 * 1024 * 1024; // 2MB.
    private const COVER_ALLOWED = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * List books with search and filters.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->authorize('books.view');

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        if ($page < 1) {
            $page = 1;
        }

        $filters = [
            'search'       => sanitize_input($_GET['search'] ?? ''),
            'category_id'  => filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) ?: null,
            'author_id'    => filter_input(INPUT_GET, 'author_id', FILTER_VALIDATE_INT) ?: null,
            'publisher_id' => filter_input(INPUT_GET, 'publisher_id', FILTER_VALIDATE_INT) ?: null,
            'shelf_id'     => filter_input(INPUT_GET, 'shelf_id', FILTER_VALIDATE_INT) ?: null,
            'status'       => in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : null,
        ];

        $bookModel = new Book();
        $result = $bookModel->paginate($page, self::PER_PAGE, array_filter($filters));

        $this->view('books.index', [
            'books'      => $result['data'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'perPage'    => $result['per_page'],
            'lastPage'   => $result['last_page'],
            'filters'    => $filters,
            'categories' => (new Category())->allActive(),
            'authors'    => (new Author())->allActive(),
            'publishers' => (new Publisher())->allActive(),
            'shelves'    => (new Shelf())->allActive(),
        ]);
    }

    /**
     * Show create book form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $this->view('books.create', [
            'categories' => (new Category())->allActive(),
            'authors'    => (new Author())->allActive(),
            'publishers' => (new Publisher())->allActive(),
            'shelves'    => (new Shelf())->allActive(),
        ]);
    }

    /**
     * Store a new book.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $data = $this->getBookInput();
        $errors = $this->validateBook($data);

        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $coverPath = upload_file($_FILES['cover_image'], 'books', self::COVER_ALLOWED, self::COVER_MAX_BYTES);

            if ($coverPath === null) {
                $errors['cover_image'] = 'Invalid cover image. Allowed: jpg, jpeg, png, webp. Max size: 2MB.';
            } else {
                $data['cover_image'] = $coverPath;
            }
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/books/create');
        }

        $bookModel = new Book();
        $bookId = $bookModel->create($data);

        Session::setFlash('success', 'Book created successfully.');
        $this->redirect('/books/show/' . $bookId);
    }

    /**
     * Show book details.
     *
     * @param int $id
     * @return void
     */
    public function show(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.view');

        $bookModel = new Book();
        $book = $bookModel->findWithRelations($id);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $copySummary = $bookModel->countCopiesByStatus($id);
        $recentCopies = (new BookCopy())->paginateByBook($id, 1, 5)['data'];

        $this->view('books.show', [
            'book'         => $book,
            'copySummary'  => $copySummary,
            'recentCopies' => $recentCopies,
        ]);
    }

    /**
     * Show edit book form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $bookModel = new Book();
        $book = $bookModel->findWithRelations($id);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $this->view('books.edit', [
            'book'       => $book,
            'categories' => (new Category())->allActive(),
            'authors'    => (new Author())->allActive(),
            'publishers' => (new Publisher())->allActive(),
            'shelves'    => (new Shelf())->allActive(),
        ]);
    }

    /**
     * Update a book.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $bookModel = new Book();
        $book = $bookModel->findWithRelations($id);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $data = $this->getBookInput();
        $data['cover_image'] = $book['cover_image'];
        $errors = $this->validateBook($data, $id);

        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $coverPath = upload_file($_FILES['cover_image'], 'books', self::COVER_ALLOWED, self::COVER_MAX_BYTES);

            if ($coverPath === null) {
                $errors['cover_image'] = 'Invalid cover image. Allowed: jpg, jpeg, png, webp. Max size: 2MB.';
            } else {
                if (!empty($book['cover_image'])) {
                    delete_file($book['cover_image']);
                }
                $data['cover_image'] = $coverPath;
            }
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/books/edit/' . $id);
        }

        $bookModel->update($id, $data);

        Session::setFlash('success', 'Book updated successfully.');
        $this->redirect('/books/show/' . $id);
    }

    /**
     * Soft delete a book.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $bookModel = new Book();
        $book = $bookModel->findWithRelations($id);

        if (!$book) {
            Session::setFlash('error', 'Book not found.');
            $this->redirect('/books');
        }

        $bookModel->softDelete($id);
        Session::setFlash('success', 'Book deleted successfully.');
        $this->redirect('/books');
    }

    /**
     * Get and sanitize book input.
     *
     * @return array
     */
    private function getBookInput(): array
    {
        return [
            'category_id'  => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0,
            'author_id'    => filter_input(INPUT_POST, 'author_id', FILTER_VALIDATE_INT) ?: 0,
            'publisher_id' => filter_input(INPUT_POST, 'publisher_id', FILTER_VALIDATE_INT) ?: null,
            'shelf_id'     => filter_input(INPUT_POST, 'shelf_id', FILTER_VALIDATE_INT) ?: null,
            'title'        => sanitize_input($_POST['title'] ?? ''),
            'isbn10'       => sanitize_input($_POST['isbn10'] ?? ''),
            'isbn13'       => sanitize_input($_POST['isbn13'] ?? ''),
            'edition'      => sanitize_input($_POST['edition'] ?? ''),
            'language'     => sanitize_input($_POST['language'] ?? 'English'),
            'publish_year' => filter_input(INPUT_POST, 'publish_year', FILTER_VALIDATE_INT) ?: null,
            'total_pages'  => filter_input(INPUT_POST, 'total_pages', FILTER_VALIDATE_INT) ?: null,
            'description'  => sanitize_input($_POST['description'] ?? ''),
            'cover_image'  => null,
            'status'       => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];
    }

    /**
     * Validate book input.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validateBook(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (trim($data['title']) === '') {
            $errors['title'] = 'Book title is required.';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Book title must not exceed 255 characters.';
        }

        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Please select a category.';
        }

        if (empty($data['author_id'])) {
            $errors['author_id'] = 'Please select an author.';
        }

        if (!empty($data['isbn13'])) {
            $bookModel = new Book();
            if ($bookModel->existsByIsbn13($data['isbn13'], $excludeId)) {
                $errors['isbn13'] = 'A book with this ISBN-13 already exists.';
            }
        }

        if (!empty($data['publish_year']) && (!preg_match('/^\d{4}$/', (string) $data['publish_year']) || $data['publish_year'] < 1000 || $data['publish_year'] > 2100)) {
            $errors['publish_year'] = 'Please enter a valid 4-digit publish year.';
        }

        if (!empty($data['total_pages']) && $data['total_pages'] < 1) {
            $errors['total_pages'] = 'Total pages must be at least 1.';
        }

        return $errors;
    }
}
