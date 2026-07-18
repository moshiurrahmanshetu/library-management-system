<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\PermissionMiddleware;

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
$permissionMiddleware = new PermissionMiddleware();

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
// Role management routes
// ------------------------------------------------------------------

$router->get('roles', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.view');
    (new \App\Controllers\RoleController())->index();
});

$router->get('roles/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.create');
    (new \App\Controllers\RoleController())->create();
});

$router->post('roles/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.create');
    (new \App\Controllers\RoleController())->store();
});

$router->get('roles/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.edit');
    (new \App\Controllers\RoleController())->edit($id);
});

$router->post('roles/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.edit');
    (new \App\Controllers\RoleController())->update($id);
});

$router->post('roles/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('roles.delete');
    (new \App\Controllers\RoleController())->destroy($id);
});

// ------------------------------------------------------------------
// Permission management routes
// ------------------------------------------------------------------

$router->get('permissions', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.view');
    (new \App\Controllers\PermissionController())->index();
});

$router->get('permissions/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.create');
    (new \App\Controllers\PermissionController())->create();
});

$router->post('permissions/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.create');
    (new \App\Controllers\PermissionController())->store();
});

$router->get('permissions/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.edit');
    (new \App\Controllers\PermissionController())->edit($id);
});

$router->post('permissions/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.edit');
    (new \App\Controllers\PermissionController())->update($id);
});

$router->post('permissions/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('permissions.delete');
    (new \App\Controllers\PermissionController())->destroy($id);
});

// ------------------------------------------------------------------
// Role permission assignment routes
// ------------------------------------------------------------------

$router->get('role-permissions', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('role_permissions.view');
    (new \App\Controllers\RolePermissionController())->index();
});

$router->get('role-permissions/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('role_permissions.view');
    (new \App\Controllers\RolePermissionController())->edit($id);
});

$router->post('role-permissions/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('role_permissions.edit');
    (new \App\Controllers\RolePermissionController())->update($id);
});

// ------------------------------------------------------------------
// User management routes
// ------------------------------------------------------------------

$router->get('users', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.view');
    (new \App\Controllers\UserController())->index();
});

$router->get('users/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.create');
    (new \App\Controllers\UserController())->create();
});

$router->post('users/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.create');
    (new \App\Controllers\UserController())->store();
});

$router->get('users/show/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.view');
    (new \App\Controllers\UserController())->show($id);
});

$router->get('users/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->edit($id);
});

$router->post('users/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->update($id);
});

$router->post('users/reset-password/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->resetPassword($id);
});

$router->post('users/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.delete');
    (new \App\Controllers\UserController())->destroy($id);
});

$router->post('users/activate/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->activate($id);
});

$router->post('users/deactivate/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->deactivate($id);
});

$router->post('users/update-role/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('users.edit');
    (new \App\Controllers\UserController())->updateRole($id);
});

// ------------------------------------------------------------------
// File upload proxy
// ------------------------------------------------------------------

$router->get('uploads/{path}', function (string $path) use ($authMiddleware, $csrfMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    (new \App\Controllers\FileController())->serve($path);
});

// ------------------------------------------------------------------
// Library master data routes (categories, authors, publishers, shelves)
// ------------------------------------------------------------------

$router->get('categories', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\CategoryController())->index();
});

$router->get('categories/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\CategoryController())->create();
});

$router->post('categories/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\CategoryController())->store();
});

$router->get('categories/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\CategoryController())->edit($id);
});

$router->post('categories/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\CategoryController())->update($id);
});

$router->post('categories/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\CategoryController())->destroy($id);
});

$router->get('authors', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\AuthorController())->index();
});

$router->get('authors/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\AuthorController())->create();
});

$router->post('authors/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\AuthorController())->store();
});

$router->get('authors/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\AuthorController())->edit($id);
});

$router->post('authors/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\AuthorController())->update($id);
});

$router->post('authors/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\AuthorController())->destroy($id);
});

$router->get('publishers', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\PublisherController())->index();
});

$router->get('publishers/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\PublisherController())->create();
});

$router->post('publishers/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\PublisherController())->store();
});

$router->get('publishers/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\PublisherController())->edit($id);
});

$router->post('publishers/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\PublisherController())->update($id);
});

$router->post('publishers/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\PublisherController())->destroy($id);
});

$router->get('shelves', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\ShelfController())->index();
});

$router->get('shelves/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\ShelfController())->create();
});

$router->post('shelves/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\ShelfController())->store();
});

$router->get('shelves/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\ShelfController())->edit($id);
});

$router->post('shelves/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\ShelfController())->update($id);
});

$router->post('shelves/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\ShelfController())->destroy($id);
});

// ------------------------------------------------------------------
// Book management routes
// ------------------------------------------------------------------

$router->get('books', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\BookController())->index();
});

$router->get('books/create', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\BookController())->create();
});

$router->post('books/store', function () use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.create');
    (new \App\Controllers\BookController())->store();
});

$router->get('books/show/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\BookController())->show($id);
});

$router->get('books/edit/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookController())->edit($id);
});

$router->post('books/update/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookController())->update($id);
});

$router->post('books/delete/{id}', function (int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\BookController())->destroy($id);
});

// ------------------------------------------------------------------
// Book copy routes
// ------------------------------------------------------------------

$router->get('books/{book_id}/copies', function (int $book_id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.view');
    (new \App\Controllers\BookCopyController())->index($book_id);
});

$router->get('books/{book_id}/copies/create', function (int $book_id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookCopyController())->create($book_id);
});

$router->post('books/{book_id}/copies/store', function (int $book_id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookCopyController())->store($book_id);
});

$router->get('books/{book_id}/copies/edit/{id}', function (int $book_id, int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookCopyController())->edit($book_id, $id);
});

$router->post('books/{book_id}/copies/update/{id}', function (int $book_id, int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.edit');
    (new \App\Controllers\BookCopyController())->update($book_id, $id);
});

$router->post('books/{book_id}/copies/delete/{id}', function (int $book_id, int $id) use ($authMiddleware, $csrfMiddleware, $permissionMiddleware) {
    $authMiddleware->handle();
    $csrfMiddleware->handle();
    $permissionMiddleware->handle('books.delete');
    (new \App\Controllers\BookCopyController())->destroy($book_id, $id);
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
