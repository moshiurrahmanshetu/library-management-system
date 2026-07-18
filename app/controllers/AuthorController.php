<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Author;

/**
 * Author controller.
 *
 * Handles CRUD for book authors.
 */
class AuthorController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * List authors.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->authorize('books.view');

        $page   = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $search = sanitize_input($_GET['search'] ?? '');

        if ($page < 1) {
            $page = 1;
        }

        $authorModel = new Author();
        $result = $authorModel->paginate($page, self::PER_PAGE, $search ?: null);

        $this->view('authors.index', [
            'authors'  => $result['data'],
            'total'    => $result['total'],
            'page'     => $result['page'],
            'perPage'  => $result['per_page'],
            'lastPage' => $result['last_page'],
            'search'   => $search,
        ]);
    }

    /**
     * Show create author form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $this->view('authors.create');
    }

    /**
     * Store a new author.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $data = [
            'full_name' => sanitize_input($_POST['full_name'] ?? ''),
            'biography' => sanitize_input($_POST['biography'] ?? ''),
            'status'    => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validate($data, ['full_name' => 'required|min:2|max:150']);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/authors/create');
        }

        $authorModel = new Author();
        $authorModel->create($data);
        Session::setFlash('success', 'Author created successfully.');
        $this->redirect('/authors');
    }

    /**
     * Show edit author form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $authorModel = new Author();
        $author = $authorModel->find($id);

        if (!$author) {
            Session::setFlash('error', 'Author not found.');
            $this->redirect('/authors');
        }

        $this->view('authors.edit', ['author' => $author]);
    }

    /**
     * Update an author.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $authorModel = new Author();
        $author = $authorModel->find($id);

        if (!$author) {
            Session::setFlash('error', 'Author not found.');
            $this->redirect('/authors');
        }

        $data = [
            'full_name' => sanitize_input($_POST['full_name'] ?? ''),
            'biography' => sanitize_input($_POST['biography'] ?? ''),
            'status'    => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validate($data, ['full_name' => 'required|min:2|max:150']);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/authors/edit/' . $id);
        }

        $authorModel->update($id, $data);
        Session::setFlash('success', 'Author updated successfully.');
        $this->redirect('/authors');
    }

    /**
     * Delete an author.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $authorModel = new Author();
        $author = $authorModel->find($id);

        if (!$author) {
            Session::setFlash('error', 'Author not found.');
            $this->redirect('/authors');
        }

        $authorModel->delete($id);
        Session::setFlash('success', 'Author deleted successfully.');
        $this->redirect('/authors');
    }
}
