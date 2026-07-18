<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Permission;

/**
 * Permission controller.
 *
 * Handles CRUD operations for permissions.
 */
class PermissionController extends Controller
{
    /**
     * List all permissions.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->authorize('permissions.view');

        $permissionModel = new Permission();

        $this->view('permissions.index', [
            'permissions' => $permissionModel->all(),
        ]);
    }

    /**
     * Show the create permission form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('permissions.create');

        $this->view('permissions.create');
    }

    /**
     * Store a new permission.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('permissions.create');

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
        ];

        $errors = $this->validatePermission($data);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/permissions/create');
        }

        $permissionModel = new Permission();
        $permissionModel->create($data);

        Session::setFlash('success', 'Permission created successfully.');
        $this->redirect('/permissions');
    }

    /**
     * Show the edit permission form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('permissions.edit');

        $permissionModel = new Permission();
        $permission = $permissionModel->find($id);

        if (!$permission) {
            Session::setFlash('error', 'Permission not found.');
            $this->redirect('/permissions');
        }

        $this->view('permissions.edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update a permission.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('permissions.edit');

        $permissionModel = new Permission();
        $permission = $permissionModel->find($id);

        if (!$permission) {
            Session::setFlash('error', 'Permission not found.');
            $this->redirect('/permissions');
        }

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
        ];

        $errors = $this->validatePermission($data, $id);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/permissions/edit/' . $id);
        }

        $permissionModel->update($id, $data);

        Session::setFlash('success', 'Permission updated successfully.');
        $this->redirect('/permissions');
    }

    /**
     * Delete a permission.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('permissions.delete');

        $permissionModel = new Permission();
        $permission = $permissionModel->find($id);

        if (!$permission) {
            Session::setFlash('error', 'Permission not found.');
            $this->redirect('/permissions');
        }

        if ($permissionModel->isAssigned($id)) {
            Session::setFlash('error', 'Cannot delete a permission that is assigned to roles.');
            $this->redirect('/permissions');
        }

        $permissionModel->delete($id);

        Session::setFlash('success', 'Permission deleted successfully.');
        $this->redirect('/permissions');
    }

    /**
     * Validate permission input.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validatePermission(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (trim($data['name']) === '') {
            $errors['name'] = 'Permission name is required.';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Permission name must not exceed 100 characters.';
        } elseif (!preg_match('/^[a-z]+(\.[a-z]+)+$/', $data['name'])) {
            $errors['name'] = 'Permission name must use lowercase dotted notation (e.g., module.action).';
        }

        if (empty($errors['name'])) {
            $permissionModel = new Permission();
            if ($permissionModel->existsByName($data['name'], $excludeId)) {
                $errors['name'] = 'A permission with this name already exists.';
            }
        }

        return $errors;
    }
}
