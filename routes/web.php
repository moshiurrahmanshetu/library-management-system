<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Web routes.
 *
 * Defines the HTTP routing table for the application.
 */

// Prevent direct access to route files.
if (!defined('ROOT_PATH')) {
    die('Direct access is not allowed.');
}

$router = new Router();
$authMiddleware = new AuthMiddleware();
$csrfMiddleware = new CsrfMiddleware();

// ------------------------------------------------------------------
// Guest routes (registration and login)
// ------------------------------------------------------------------

$router->get('register', function () use ($authMiddleware) {
    $authMiddleware->handle(true);
    (new \App\Controllers\AuthController())->showRegister();
});

$router->post('register', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle(true);
    $csrfMiddleware->handle();
    (new \App\Controllers\AuthController())->register();
});

$router->get('login', function () use ($authMiddleware) {
    $authMiddleware->handle(true);
    (new \App\Controllers\AuthController())->showLogin();
});

$router->post('login', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle(true);
    $csrfMiddleware->handle();
    (new \App\Controllers\AuthController())->login();
});

// ------------------------------------------------------------------
// Authenticated routes
// ------------------------------------------------------------------

$router->get('dashboard', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\DashboardController())->index();
});

$router->get('profile', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\ProfileController())->show();
});

$router->post('profile', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\ProfileController())->update();
});

$router->get('password/change', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\PasswordController())->showChange();
});

$router->post('password/change', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\PasswordController())->change();
});

// Logout can be triggered from a link; treat it as POST for safety via a small form.
$router->post('logout', function () use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\AuthController())->logout();
});

// ------------------------------------------------------------------
// Default redirects
// ------------------------------------------------------------------

$router->get('', function () {
    if (\App\Core\Session::get('user_id')) {
        redirect('/dashboard');
    }
    redirect('/login');
});

$router->dispatch();
