<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Authentication controller.
 *
 * Handles user registration, login, logout and remember-me cookies.
 */
class AuthController extends Controller
{
    /**
     * Name of the remember-me cookie.
     *
     * @var string
     */
    private const REMEMBER_COOKIE = 'remember';

    /**
     * Show the registration form.
     *
     * @return void
     */
    public function showRegister(): void
    {
        $this->requireGuest();
        $this->view('auth.register');
    }

    /**
     * Process a registration request.
     *
     * @return void
     */
    public function register(): void
    {
        $this->requireGuest();

        $data = $this->getRegistrationData();
        $errors = $this->validate($data, [
            'name'                  => 'required|min:2|max:100',
            'email'                 => 'required|email|max:150',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $userModel = new User();

        if ($userModel->emailExists($data['email'])) {
            $errors['email'] = 'This email address is already registered.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/register');
        }

        $userId = $userModel->create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password_hash'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'role_id'        => 4, // Reader is assigned automatically.
            'status'         => 'active',
        ]);

        Session::regenerate(true);
        Session::set('user_id', $userId);
        Session::set('user_email', $data['email']);
        Session::set('user_name', $data['name']);
        Session::updateActivity();
        refresh_permissions();

        Session::setFlash('success', 'Welcome! Your account has been created.');
        $this->redirect('/dashboard');
    }

    /**
     * Show the login form.
     *
     * @return void
     */
    public function showLogin(): void
    {
        $this->requireGuest();
        $this->view('auth.login');
    }

    /**
     * Process a login request.
     *
     * @return void
     */
    public function login(): void
    {
        $this->requireGuest();

        $data = [
            'email'    => sanitize_email($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'remember' => isset($_POST['remember']),
        ];

        $errors = $this->validate($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            Session::setFlash('old', $data);
            Session::setFlash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Your account is inactive. Please contact the administrator.');
            $this->redirect('/login');
        }

        Session::regenerate(true);
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['name']);
        Session::updateActivity();
        refresh_permissions();

        $userModel->touchLastLogin($user['id']);

        if ($data['remember']) {
            $this->setRememberCookie($user['id']);
        }

        Session::setFlash('success', 'You have logged in successfully.');
        $this->redirect('/dashboard');
    }

    /**
     * Log the current user out.
     *
     * @return void
     */
    public function logout(): void
    {
        $userId = Session::get('user_id');

        if ($userId) {
            $userModel = new User();
            $userModel->clearRememberToken((int) $userId);
        }

        if (isset($_COOKIE[self::REMEMBER_COOKIE])) {
            setcookie(self::REMEMBER_COOKIE, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        Session::destroy();

        $this->redirect('/login');
    }

    /**
     * Sanitize and return registration input.
     *
     * @return array
     */
    private function getRegistrationData(): array
    {
        return [
            'name'                  => sanitize_input($_POST['name'] ?? ''),
            'email'                 => sanitize_email($_POST['email'] ?? ''),
            'password'              => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];
    }

    /**
     * Create and store a secure remember-me cookie.
     *
     * @param int $userId
     * @return void
     */
    private function setRememberCookie(int $userId): void
    {
        $selector  = bin2hex(random_bytes(12)); // 24 hex characters.
        $validator = bin2hex(random_bytes(32)); // 64 hex characters.
        $validatorHash = hash('sha256', $validator);

        $userModel = new User();
        $userModel->updateRememberToken($userId, $selector, $validatorHash);

        $cookieValue = $selector . ':' . $validator;

        setcookie(self::REMEMBER_COOKIE, $cookieValue, [
            'expires'  => time() + REMEMBER_COOKIE_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
