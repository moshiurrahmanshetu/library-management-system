<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Role;
use App\Models\User;

/**
 * User management controller.
 *
 * Handles listing, searching, viewing, editing, activating, deactivating
 * and assigning roles to users.
 */
class UserController extends Controller
{
    /**
     * Number of users per page.
     *
     * @var int
     */
    private const PER_PAGE = 10;

    /**
     * List users with search and pagination.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->authorize('users.view');

        $page   = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $search = sanitize_input($_GET['search'] ?? '');

        if ($page < 1) {
            $page = 1;
        }

        $userModel = new User();
        $result = $userModel->paginate($page, self::PER_PAGE, $search ?: null);

        $this->view('users.index', [
            'users'   => $result['data'],
            'total'   => $result['total'],
            'page'    => $result['page'],
            'perPage' => $result['per_page'],
            'lastPage'=> $result['last_page'],
            'search'  => $search,
        ]);
    }

    /**
     * Show user details.
     *
     * @param int $id
     * @return void
     */
    public function show(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.view');

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $this->view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the admin edit user form.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $roleModel = new Role();

        $this->view('users.edit', [
            'user'            => $user,
            'roles'           => $roleModel->all(),
            'currentUserRoleId' => (int) $this->user()['role_id'],
        ]);
    }

    /**
     * Update a user (admin).
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $data = [
            'name'    => sanitize_input($_POST['name'] ?? ''),
            'email'   => sanitize_email($_POST['email'] ?? ''),
            'role_id' => filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT) ?: $user['role_id'],
            'status'  => in_array($_POST['status'] ?? '', ['active', 'inactive'], true)
                ? $_POST['status']
                : $user['status'],
        ];

        $errors = $this->validate($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
        ]);

        if ($userModel->emailExists($data['email'], $id)) {
            $errors['email'] = 'This email address is already in use.';
        }

        $roleModel = new Role();
        if (!$roleModel->find((int) $data['role_id'])) {
            $errors['role_id'] = 'The selected role is invalid.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/users/edit/' . $id);
        }

        $userModel->updateByAdmin($id, $data);

        // Refresh cached permissions if the user edited their own role.
        if ((int) Session::get('user_id') === $id) {
            refresh_permissions();
        }

        Session::setFlash('success', 'User updated successfully.');
        $this->redirect('/users');
    }

    /**
     * Activate a user.
     *
     * @param int $id
     * @return void
     */
    public function activate(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        $this->updateStatus($id, 'active');
    }

    /**
     * Deactivate a user.
     *
     * @param int $id
     * @return void
     */
    public function deactivate(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        $this->updateStatus($id, 'inactive');
    }

    /**
     * Update a user's role (shortcut action).
     *
     * @param int $id
     * @return void
     */
    public function updateRole(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        // Only Super Admin can change roles.
        if ((int) $this->user()['role_id'] !== 1) {
            Session::setFlash('error', 'You are not authorized to change user roles.');
            $this->redirect('/users');
        }

        $roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

        $roleModel = new Role();
        if (!$roleId || !$roleModel->find($roleId)) {
            Session::setFlash('error', 'Invalid role selected.');
            $this->redirect('/users');
        }

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $userModel->updateRole($id, $roleId);

        if ((int) Session::get('user_id') === $id) {
            refresh_permissions();
        }

        Session::setFlash('success', 'User role updated successfully.');
        $this->redirect('/users');
    }

    /**
     * Helper to update a user's status.
     *
     * @param int $id
     * @param string $status
     * @return void
     */
    private function updateStatus(int $id, string $status): void
    {
        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $userModel->updateStatus($id, $status);

        Session::setFlash('success', 'User ' . ($status === 'active' ? 'activated' : 'deactivated') . ' successfully.');
        $this->redirect('/users');
    }
}
