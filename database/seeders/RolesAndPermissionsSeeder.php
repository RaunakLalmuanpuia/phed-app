<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'request-edit', 'request-delete', 'request-transfer',
            'approve-edit', 'approve-delete', 'approve-transfer',
            'view-employee','view-allemployee','create-employee','delete-employee','edit-employee',
            'view-anyuser','view-user','create-user','delete-user','edit-user',
            'view-anyoffice','view-office','create-office','delete-office','edit-office',
            'view-anyrole','edit-role',
            'view-any-document-type','view-document-type','create-document-type','delete-document-type','edit-document-type',

        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'Manager' => ['request-edit', 'request-delete', 'request-transfer',],
            'Admin' => ['approve-edit', 'approve-delete', 'approve-transfer',
                'view-employee','view-allemployee','create-employee','delete-employee','edit-employee',
                'view-anyuser','view-user','create-user','delete-user','edit-user',
                'view-anyoffice','view-office','create-office','delete-office','edit-office',
                'view-anyrole','edit-role',
                'view-any-document-type','view-document-type','create-document-type','delete-document-type','edit-document-type',
                ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
