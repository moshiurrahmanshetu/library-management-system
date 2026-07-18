<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Publisher;

/**
 * Publisher controller.
 *
 * Handles CRUD for book publishers.
 */
class PublisherController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * List publishers.
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

        $publisherModel = new Publisher();
        $result = $publisherModel->paginate($page, self::PER_PAGE, $search ?: null);

        $this->view('publishers.index', [
            'publishers' => $result['data'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'perPage'    => $result['per_page'],
            'lastPage'   => $result['last_page'],
            'search'     => $search,
        ]);
    }

    /**
     * Show create publisher form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $this->view('publishers.create');
    }

    /**
     * Store a new publisher.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $data = [
            'name'    => sanitize_input($_POST['name'] ?? ''),
            'phone'   => sanitize_input($_POST['phone'] ?? ''),
            'email'   => sanitize_email($_POST['email'] ?? ''),
            'website' => sanitize_input($_POST['website'] ?? ''),
            'address' => sanitize_input($_POST['address'] ?? ''),
            'status'  => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validatePublisher($data);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/publishers/create');
        }

        $publisherModel = new Publisher();
        $publisherModel->create($data);
        Session::setFlash('success', 'Publisher created successfully.');
        $this->redirect('/publishers');
    }

    /**
     * Show edit publisher form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $publisherModel = new Publisher();
        $publisher = $publisherModel->find($id);

        if (!$publisher) {
            Session::setFlash('error', 'Publisher not found.');
            $this->redirect('/publishers');
        }

        $this->view('publishers.edit', ['publisher' => $publisher]);
    }

    /**
     * Update a publisher.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $publisherModel = new Publisher();
        $publisher = $publisherModel->find($id);

        if (!$publisher) {
            Session::setFlash('error', 'Publisher not found.');
            $this->redirect('/publishers');
        }

        $data = [
            'name'    => sanitize_input($_POST['name'] ?? ''),
            'phone'   => sanitize_input($_POST['phone'] ?? ''),
            'email'   => sanitize_email($_POST['email'] ?? ''),
            'website' => sanitize_input($_POST['website'] ?? ''),
            'address' => sanitize_input($_POST['address'] ?? ''),
            'status'  => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validatePublisher($data, $id);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/publishers/edit/' . $id);
        }

        $publisherModel->update($id, $data);
        Session::setFlash('success', 'Publisher updated successfully.');
        $this->redirect('/publishers');
    }

    /**
     * Delete a publisher.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $publisherModel = new Publisher();
        $publisher = $publisherModel->find($id);

        if (!$publisher) {
            Session::setFlash('error', 'Publisher not found.');
            $this->redirect('/publishers');
        }

        $publisherModel->delete($id);
        Session::setFlash('success', 'Publisher deleted successfully.');
        $this->redirect('/publishers');
    }

    /**
     * Validate publisher input.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validatePublisher(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (trim($data['name']) === '') {
            $errors['name'] = 'Publisher name is required.';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Publisher name must not exceed 100 characters.';
        }

        if (empty($errors['name'])) {
            $publisherModel = new Publisher();
            if ($publisherModel->existsByName($data['name'], $excludeId)) {
                $errors['name'] = 'A publisher with this name already exists.';
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Please enter a valid URL.';
        }

        return $errors;
    }
}
