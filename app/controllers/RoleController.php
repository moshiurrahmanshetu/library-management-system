<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Role;

/**
 * Role controller.
 *
 * Handles CRUD operations for roles.
 */
class RoleController extends Controller
{
    /**
     * List all roles.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->authorize('roles.view');

        $roleModel = new Role();

        $this->view('roles.index', [
            'roles' => $roleModel->all(),
        ]);
    }

    /**
     * Show the create role form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('roles.create');

        $this->view('roles.create');
    }

    /**
     * Store a new role.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('roles.create');

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:50',
        ]);

        $roleModel = new Role();

        if ($roleModel->existsByName($data['name'])) {
            $errors['name'] = 'A role with this name already exists.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/roles/create');
        }

        $roleModel->create($data);

        Session::setFlash('success', 'Role created successfully.');
        $this->redirect('/roles');
    }

    /**
     * Show the edit role form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('roles.edit');

        $roleModel = new Role();
        $role = $roleModel->find($id);

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        $this->view('roles.edit', [
            'role' => $role,
        ]);
    }

    /**
     * Update a role.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('roles.edit');

        $roleModel = new Role();
        $role = $roleModel->find($id);

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        $data = [
            'name'        => sanitize_input($_POST['name'] ?? ''),
            'description' => sanitize_input($_POST['description'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:50',
        ]);

        if ($roleModel->existsByName($data['name'], $id)) {
            $errors['name'] = 'A role with this name already exists.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/roles/edit/' . $id);
        }

        $roleModel->update($id, $data);

        Session::setFlash('success', 'Role updated successfully.');
        $this->redirect('/roles');
    }

    /**
     * Delete a role.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('roles.delete');

        if ($id === 1) {
            Session::setFlash('error', 'The Super Admin role cannot be deleted.');
            $this->redirect('/roles');
        }

        $roleModel = new Role();
        $role = $roleModel->find($id);

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        if ($roleModel->hasUsers($id)) {
            Session::setFlash('error', 'Cannot delete a role that is assigned to users.');
            $this->redirect('/roles');
        }

        $roleModel->delete($id);

        Session::setFlash('success', 'Role deleted successfully.');
        $this->redirect('/roles');
    }
}
