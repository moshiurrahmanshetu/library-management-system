<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Permission;
use App\Models\Role;

/**
 * Role permission controller.
 *
 * Handles assigning permissions to roles.
 */
class RolePermissionController extends Controller
{
    /**
     * Show the permission assignment form for a role.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        $this->authorize('role_permissions.view');

        $roleModel = new Role();
        $role = $roleModel->find($id);

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        $permissionModel = new Permission();

        $this->view('role_permissions.edit', [
            'role'                  => $role,
            'groupedPermissions'    => $permissionModel->groupedByModule(),
            'assignedPermissionIds' => $permissionModel->getIdsByRole($id),
        ]);
    }

    /**
     * Update permissions for a role.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->authorize('role_permissions.edit');

        $roleModel = new Role();
        $role = $roleModel->find($id);

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        $permissionIds = $_POST['permissions'] ?? [];

        if (!is_array($permissionIds)) {
            $permissionIds = [];
        }

        // Ensure Super Admin always keeps all permissions.
        if ($id === 1) {
            $permissionModel = new Permission();
            $allPermissions = $permissionModel->all();
            $permissionIds = array_column($allPermissions, 'id');
        }

        $permissionModel = new Permission();
        $success = $permissionModel->syncForRole($id, $permissionIds);

        if ($success) {
            Session::setFlash('success', 'Role permissions updated successfully.');
        } else {
            Session::setFlash('error', 'Failed to update role permissions. Please try again.');
        }

        $this->redirect('/role-permissions/edit/' . $id);
    }

}
