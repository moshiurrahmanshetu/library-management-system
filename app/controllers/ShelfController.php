<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Shelf;

/**
 * Shelf controller.
 *
 * Handles CRUD for library shelves.
 */
class ShelfController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * List shelves.
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

        $shelfModel = new Shelf();
        $result = $shelfModel->paginate($page, self::PER_PAGE, $search ?: null);

        $this->view('shelves.index', [
            'shelves'  => $result['data'],
            'total'    => $result['total'],
            'page'     => $result['page'],
            'perPage'  => $result['per_page'],
            'lastPage' => $result['last_page'],
            'search'   => $search,
        ]);
    }

    /**
     * Show create shelf form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $this->view('shelves.create');
    }

    /**
     * Store a new shelf.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $data = [
            'shelf_code'  => sanitize_input($_POST['shelf_code'] ?? ''),
            'shelf_name'  => sanitize_input($_POST['shelf_name'] ?? ''),
            'floor'       => sanitize_input($_POST['floor'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
            'status'      => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validateShelf($data);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/shelves/create');
        }

        $shelfModel = new Shelf();
        $shelfModel->create($data);
        Session::setFlash('success', 'Shelf created successfully.');
        $this->redirect('/shelves');
    }

    /**
     * Show edit shelf form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $shelfModel = new Shelf();
        $shelf = $shelfModel->find($id);

        if (!$shelf) {
            Session::setFlash('error', 'Shelf not found.');
            $this->redirect('/shelves');
        }

        $this->view('shelves.edit', ['shelf' => $shelf]);
    }

    /**
     * Update a shelf.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $shelfModel = new Shelf();
        $shelf = $shelfModel->find($id);

        if (!$shelf) {
            Session::setFlash('error', 'Shelf not found.');
            $this->redirect('/shelves');
        }

        $data = [
            'shelf_code'  => sanitize_input($_POST['shelf_code'] ?? ''),
            'shelf_name'  => sanitize_input($_POST['shelf_name'] ?? ''),
            'floor'       => sanitize_input($_POST['floor'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
            'status'      => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validateShelf($data, $id);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/shelves/edit/' . $id);
        }

        $shelfModel->update($id, $data);
        Session::setFlash('success', 'Shelf updated successfully.');
        $this->redirect('/shelves');
    }

    /**
     * Delete a shelf.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $shelfModel = new Shelf();
        $shelf = $shelfModel->find($id);

        if (!$shelf) {
            Session::setFlash('error', 'Shelf not found.');
            $this->redirect('/shelves');
        }

        $shelfModel->delete($id);
        Session::setFlash('success', 'Shelf deleted successfully.');
        $this->redirect('/shelves');
    }

    /**
     * Validate shelf input.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validateShelf(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (trim($data['shelf_code']) === '') {
            $errors['shelf_code'] = 'Shelf code is required.';
        } elseif (strlen($data['shelf_code']) > 50) {
            $errors['shelf_code'] = 'Shelf code must not exceed 50 characters.';
        }

        if (empty($errors['shelf_code'])) {
            $shelfModel = new Shelf();
            if ($shelfModel->existsByCode($data['shelf_code'], $excludeId)) {
                $errors['shelf_code'] = 'A shelf with this code already exists.';
            }
        }

        if (trim($data['shelf_name']) === '') {
            $errors['shelf_name'] = 'Shelf name is required.';
        } elseif (strlen($data['shelf_name']) > 100) {
            $errors['shelf_name'] = 'Shelf name must not exceed 100 characters.';
        }

        return $errors;
    }
}
