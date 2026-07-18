<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Dashboard controller.
 *
 * Displays the authenticated user's home dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAuth();

        $user = $this->user();

        // Simple placeholder for profile completion percentage.
        $profileCompletion = $this->calculateProfileCompletion($user);

        $this->view('dashboard.index', [
            'user'              => $user,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Calculate a mock profile completion percentage.
     *
     * In future phases this can be expanded with real fields.
     *
     * @param array $user
     * @return int
     */
    private function calculateProfileCompletion(array $user): int
    {
        $completed = 0;
        $total = 3;

        if (!empty($user['name'])) {
            $completed++;
        }

        if (!empty($user['email'])) {
            $completed++;
        }

        if (!empty($user['last_login_at'])) {
            $completed++;
        }

        return (int) round(($completed / $total) * 100);
    }
}
