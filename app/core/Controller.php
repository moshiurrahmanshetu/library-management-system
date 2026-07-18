<?php

namespace App\Core;

use App\Models\User;

/**
 * Base Controller class.
 *
 * Provides shared functionality for all controllers such as view rendering,
 * redirects, authentication checks and current user retrieval.
 */
abstract class Controller
{
    /**
     * Cached current authenticated user.
     *
     * @var array|null
     */
    private ?array $currentUser = null;

    /**
     * Render a view with optional data.
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function view(string $view, array $data = []): void
    {
        $viewPath = ROOT_PATH . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        extract($data, EXTR_SKIP);

        require $viewPath;
    }

    /**
     * Redirect to a given URL or path.
     *
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        if (!str_starts_with($url, 'http')) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }

        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if a user is currently authenticated.
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return Session::get('user_id') !== null;
    }

    /**
     * Require an authenticated user or redirect to login.
     *
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            Session::setFlash('error', 'Please log in to continue.');
            $this->redirect('/login');
        }
    }

    /**
     * Require a guest user or redirect to dashboard.
     *
     * @return void
     */
    protected function requireGuest(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @return array|null
     */
    protected function user(): ?array
    {
        if ($this->currentUser !== null) {
            return $this->currentUser;
        }

        $userId = Session::get('user_id');

        if (!$userId) {
            return null;
        }

        $userModel = new User();
        $this->currentUser = $userModel->findById((int) $userId);

        return $this->currentUser;
    }

    /**
     * Require the authenticated user to have a specific permission.
     *
     * @param string $permission
     * @return void
     */
    protected function authorize(string $permission): void
    {
        if (!can($permission)) {
            $this->render403();
        }
    }

    /**
     * Render the 403 forbidden page and stop execution.
     *
     * @return void
     */
    protected function render403(): void
    {
        http_response_code(403);

        $viewPath = ROOT_PATH . '/resources/views/errors/403.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '403 - Forbidden.';
        }

        exit;
    }

    /**
     * Validate input data against a set of rules.
     *
     * @param array $data
     * @param array $rules
     * @return array Associative array of field => error message.
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleList = explode('|', $ruleSet);
            $value    = $data[$field] ?? '';

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && trim($value) === '') {
                    $errors[$field] = ucfirst($field) . ' is required.';
                    break;
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Please enter a valid email address.';
                    break;
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least {$min} characters.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed {$max} characters.";
                        break;
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmField = $field . '_confirmation';
                    if (($data[$confirmField] ?? '') !== $value) {
                        $errors[$field] = ucfirst($field) . ' confirmation does not match.';
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
