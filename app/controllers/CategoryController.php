<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Category;

/**
 * Category controller.
 *
 * Handles CRUD for book categories.
 */
class CategoryController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * List categories.
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

        $categoryModel = new Category();
        $result = $categoryModel->paginate($page, self::PER_PAGE, $search ?: null);

        $this->view('categories.index', [
            'categories' => $result['data'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'perPage'    => $result['per_page'],
            'lastPage'   => $result['last_page'],
            'search'     => $search,
        ]);
    }

    /**
     * Show create category form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $this->view('categories.create');
    }

    /**
     * Store a new category.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('books.create');

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
            'status'      => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validate($data, ['name' => 'required|min:2|max:100']);

        $categoryModel = new Category();
        if ($categoryModel->existsByName($data['name'])) {
            $errors['name'] = 'A category with this name already exists.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/categories/create');
        }

        $categoryModel->create($data);
        Session::setFlash('success', 'Category created successfully.');
        $this->redirect('/categories');
    }

    /**
     * Show edit category form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::setFlash('error', 'Category not found.');
            $this->redirect('/categories');
        }

        $this->view('categories.edit', ['category' => $category]);
    }

    /**
     * Update a category.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.edit');

        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::setFlash('error', 'Category not found.');
            $this->redirect('/categories');
        }

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
            'status'      => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ];

        $errors = $this->validate($data, ['name' => 'required|min:2|max:100']);

        if ($categoryModel->existsByName($data['name'], $id)) {
            $errors['name'] = 'A category with this name already exists.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/categories/edit/' . $id);
        }

        $categoryModel->update($id, $data);
        Session::setFlash('success', 'Category updated successfully.');
        $this->redirect('/categories');
    }

    /**
     * Delete a category.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('books.delete');

        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::setFlash('error', 'Category not found.');
            $this->redirect('/categories');
        }

        $categoryModel->delete($id);
        Session::setFlash('success', 'Category deleted successfully.');
        $this->redirect('/categories');
    }
}
