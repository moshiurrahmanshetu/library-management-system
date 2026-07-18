<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Password controller.
 *
 * Handles password change requests for authenticated users.
 */
class PasswordController extends Controller
{
    /**
     * Show the change password form.
     *
     * @return void
     */
    public function showChange(): void
    {
        $this->requireAuth();
        $this->view('password.change');
    }

    /**
     * Process a password change request.
     *
     * @return void
     */
    public function change(): void
    {
        $this->requireAuth();

        $user = $this->user();

        $data = [
            'current_password'            => $_POST['current_password'] ?? '',
            'password'                    => $_POST['password'] ?? '',
            'password_confirmation'       => $_POST['password_confirmation'] ?? '',
        ];

        $errors = $this->validate($data, [
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (!password_verify($data['current_password'], $user['password_hash'])) {
            $errors['current_password'] = 'The current password is incorrect.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/password/change');
        }

        $userModel = new User();
        $userModel->updatePassword((int) $user['id'], password_hash($data['password'], PASSWORD_BCRYPT));

        Session::setFlash('success', 'Your password has been changed successfully.');
        $this->redirect('/password/change');
    }
}
