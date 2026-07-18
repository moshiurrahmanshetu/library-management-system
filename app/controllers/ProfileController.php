<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Profile controller.
 *
 * Handles viewing and updating the authenticated user's profile.
 */
class ProfileController extends Controller
{
    /**
     * Show the profile page.
     *
     * @return void
     */
    public function show(): void
    {
        $this->requireAuth();

        $this->view('profile.index', [
            'user' => $this->user(),
        ]);
    }

    /**
     * Process a profile update request.
     *
     * @return void
     */
    public function update(): void
    {
        $this->requireAuth();

        $user = $this->user();

        $data = [
            'name'  => sanitize_input($_POST['name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
        ]);

        $userModel = new User();

        if ($userModel->emailExists($data['email'], (int) $user['id'])) {
            $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/profile');
        }

        $userModel->updateProfile((int) $user['id'], $data);

        // Refresh session-stored user name/email.
        Session::set('user_name', $data['name']);
        Session::set('user_email', $data['email']);

        Session::setFlash('success', 'Your profile has been updated.');
        $this->redirect('/profile');
    }
}
