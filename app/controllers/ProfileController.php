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
        $userId = (int) $user['id'];

        $data = [
            'name'           => sanitize_input($_POST['name'] ?? ''),
            'email'          => sanitize_email($_POST['email'] ?? ''),
            'phone'          => !empty($_POST['phone']) ? sanitize_input($_POST['phone']) : null,
            'gender'         => !empty($_POST['gender']) ? sanitize_input($_POST['gender']) : null,
            'date_of_birth'  => !empty($_POST['date_of_birth']) ? sanitize_input($_POST['date_of_birth']) : null,
            'address'        => !empty($_POST['address']) ? sanitize_input($_POST['address']) : null,
            'profile_photo'  => null,
        ];

        $errors = $this->validate($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
        ]);

        $userModel = new User();

        if ($userModel->emailExists($data['email'], $userId)) {
            $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($data['phone']) && $userModel->phoneExists($data['phone'], $userId)) {
            $errors['phone'] = 'This phone number is already in use.';
        }

        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors['profile_photo'] = 'Invalid file type. Only JPG, PNG, and WEBP files are allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB
                $errors['profile_photo'] = 'File size must be less than 2MB.';
            } else {
                $oldPhotoPath = $userModel->getProfilePhoto($userId);
                $uploadedPath = upload_file($file, 'profile', $allowedExtensions, 2 * 1024 * 1024);
                
                if ($uploadedPath) {
                    $data['profile_photo'] = $uploadedPath;
                    // Delete old photo if exists
                    if ($oldPhotoPath) {
                        delete_file($oldPhotoPath);
                    }
                }
            }
        }

        if (!empty($errors)) {
            Session::setFlash('old', $data);
            Session::setFlash('errors', $errors);
            $this->redirect('/profile');
        }

        $userModel->updateProfile($userId, $data);

        // Refresh session-stored user name/email.
        Session::set('user_name', $data['name']);
        Session::set('user_email', $data['email']);

        Session::setFlash('success', 'Your profile has been updated.');
        $this->redirect('/profile');
    }
}
