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
            'lastPage' => $result['last_page'],
            'search'  => $search,
        ]);
    }

    /**
     * Show the create user form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->authorize('users.create');

        $roleModel = new Role();
        $roles = $roleModel->all();

        $this->view('users.create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a new user.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->authorize('users.create');

        $data = [
            'name' => sanitize_input($_POST['name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'username' => !empty($_POST['username']) ? sanitize_input($_POST['username']) : null,
            'phone' => !empty($_POST['phone']) ? sanitize_input($_POST['phone']) : null,
            'role_id' => filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT) ?: 4,
            'status' => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'gender' => !empty($_POST['gender']) ? sanitize_input($_POST['gender']) : null,
            'date_of_birth' => !empty($_POST['date_of_birth']) ? sanitize_input($_POST['date_of_birth']) : null,
            'address' => !empty($_POST['address']) ? sanitize_input($_POST['address']) : null,
            'notes' => !empty($_POST['notes']) ? sanitize_input($_POST['notes']) : null,
        ];

        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|min:8',
        ]);

        if (empty($data['password']) !== empty($data['password_confirmation'])) {
            $errors['password'] = 'Passwords do not match.';
        }

        $userModel = new User();
        if ($userModel->emailExists($data['email'])) {
            $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($data['username']) && $userModel->usernameExists($data['username'])) {
            $errors['username'] = 'This username is already in use.';
        }

        if (!empty($data['phone']) && $userModel->phoneExists($data['phone'])) {
            $errors['phone'] = 'This phone number is already in use.';
        }

        $roleModel = new Role();
        if (!$roleModel->find((int)$data['role_id'])) {
            $errors['role_id'] = 'The selected role is invalid.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/users/create');
        }

        $userModel->create([
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'status' => $data['status'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'],
            'notes' => $data['notes'],
        ]);

        Session::setFlash('success', 'User created successfully.');
        $this->redirect('/users');
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
            'currentUserRoleId' => (int)$this->user()['role_id'],
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
            'username' => !empty($_POST['username']) ? sanitize_input($_POST['username']) : null,
            'phone' => !empty($_POST['phone']) ? sanitize_input($_POST['phone']) : null,
            'role_id' => filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT) ?: $user['role_id'],
            'status'  => in_array($_POST['status'] ?? '', ['active', 'inactive'], true)
                ? $_POST['status']
                : $user['status'],
            'gender' => !empty($_POST['gender']) ? sanitize_input($_POST['gender']) : null,
            'date_of_birth' => !empty($_POST['date_of_birth']) ? sanitize_input($_POST['date_of_birth']) : null,
            'address' => !empty($_POST['address']) ? sanitize_input($_POST['address']) : null,
            'notes' => !empty($_POST['notes']) ? sanitize_input($_POST['notes']) : null,
        ];

        $errors = $this->validate($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
        ]);

        if ($userModel->emailExists($data['email'], $id)) {
            $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($data['username']) && $userModel->usernameExists($data['username'], $id)) {
            $errors['username'] = 'This username is already in use.';
        }

        if (!empty($data['phone']) && $userModel->phoneExists($data['phone'], $id)) {
            $errors['phone'] = 'This phone number is already in use.';
        }

        $roleModel = new Role();
        if (!$roleModel->find((int)$data['role_id'])) {
            $errors['role_id'] = 'The selected role is invalid.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/users/edit/' . $id);
        }

        $userModel->updateByAdmin($id, $data);

        // Refresh cached permissions if the user edited their own role.
        if ((int)Session::get('user_id') === $id) {
            refresh_permissions();
        }

        Session::setFlash('success', 'User updated successfully.');
        $this->redirect('/users');
    }

    /**
     * Reset a user's password.
     *
     * @param int $id
     * @return void
     */
    public function resetPassword(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.edit');

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if (empty($password)) {
            Session::setFlash('errors', ['password' => 'Password is required.']);
            $this->redirect('/users/edit/' . $id);
        }

        if ($password !== $passwordConfirmation) {
            Session::setFlash('errors', ['password' => 'Passwords do not match.']);
            $this->redirect('/users/edit/' . $id);
        }

        if (strlen($password) < 8) {
            Session::setFlash('errors', ['password' => 'Password must be at least 8 characters.']);
            $this->redirect('/users/edit/' . $id);
        }

        $userModel->updatePassword($id, password_hash($password, PASSWORD_BCRYPT));

        Session::setFlash('success', 'User password reset successfully.');
        $this->redirect('/users/edit/' . $id);
    }

    /**
     * Soft delete a user.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->authorize('users.delete');

        // Don't allow deleting yourself or Super Admin
        if ((int)$this->user()['id'] === $id || $id === 1) {
            Session::setFlash('error', 'You cannot delete this user.');
            $this->redirect('/users');
        }

        $userModel = new User();
        $user = $userModel->findDetailed($id);

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $userModel->softDelete($id);

        Session::setFlash('success', 'User deleted successfully.');
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
        if ((int)$this->user()['role_id'] !== 1) {
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

        if ((int)Session::get('user_id') === $id) {
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
