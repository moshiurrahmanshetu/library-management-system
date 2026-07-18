<?php

namespace App\Middleware;

use App\Core\Session;
use App\Models\User;

/**
 * Authentication middleware.
 *
 * Ensures guests are redirected to login and authenticated users are
 * loaded from the remember-me cookie when the session has expired.
 */
class AuthMiddleware
{
    /**
     * Cookie name used for the remember-me token.
     *
     * @var string
     */
    private const REMEMBER_COOKIE = 'remember';

    /**
     * Handle an incoming request.
     *
     * @param bool $requireGuest If true, redirect authenticated users away.
     * @return void
     */
    public function handle(bool $requireGuest = false): void
    {
        Session::start();

        if ($requireGuest) {
            if (Session::get('user_id')) {
                redirect('/dashboard');
            }
            return;
        }

        // Authenticate via session or remember-me cookie.
        if (!Session::get('user_id')) {
            $this->attemptCookieLogin();
        }

        if (!Session::get('user_id')) {
            Session::setFlash('error', 'Please log in to continue.');
            redirect('/login');
        }

        // Enforce session timeout for active sessions.
        if (Session::isExpired()) {
            Session::destroy();
            $this->clearRememberCookie();
            Session::setFlash('error', 'Your session has expired. Please log in again.');
            redirect('/login');
        }

        Session::updateActivity();
    }

    /**
     * Attempt to log the user in via a remember-me cookie.
     *
     * @return void
     */
    private function attemptCookieLogin(): void
    {
        $cookie = $_COOKIE[self::REMEMBER_COOKIE] ?? '';

        if (empty($cookie) || !str_contains($cookie, ':')) {
            return;
        }

        [$selector, $validator] = explode(':', $cookie, 2);

        if (empty($selector) || empty($validator)) {
            return;
        }

        $userModel = new User();
        $user = $userModel->findByRememberSelector($selector);

        if (!$user) {
            $this->clearRememberCookie();
            return;
        }

        $validatorHash = hash('sha256', $validator);

        if (!hash_equals($user['remember_token'], $validatorHash)) {
            $this->clearRememberCookie();
            return;
        }

        // Token is valid: start a fresh session.
        Session::regenerate(true);
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['name']);
        Session::updateActivity();

        $userModel->touchLastLogin($user['id']);
    }

    /**
     * Clear the remember-me cookie from the browser.
     *
     * @return void
     */
    private function clearRememberCookie(): void
    {
        setcookie(self::REMEMBER_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
