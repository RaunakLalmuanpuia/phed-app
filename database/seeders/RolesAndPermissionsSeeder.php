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
            'request-edit', 'request-delete', 'request-transfer','request-document-edit',
            'approve-edit', 'approve-delete', 'approve-transfer', 'approve-document-edit',
            'transfer-employee','delete-transfer','request-document-delete','approve-document-delete',
            'view-allemployee','view-wc-employee','view-pe-employee','view-mr-employee','view-scheme-employee','view-deleted-employee','view-master-employee',
            'view-employee','create-employee','delete-employee','edit-employee','edit-delete','delete-document',
            'view-anyuser','view-user','create-user','delete-user','edit-user',
            'view-anyoffice','view-office','create-office','delete-office','edit-office',
            'view-anyscheme','view-scheme','create-scheme','delete-scheme','edit-scheme',
            'view-anyrole','edit-role',
            'view-any-document-type','view-document-type','create-document-type','delete-document-type','edit-document-type',
            'import-employee', 'export-employee',
            'view-remuneration','create-remuneration', 'edit-remuneration','delete-remuneration','bulk-update-remuneration',
            'view-engagement-card','store-engagement-card','download-engagement-card',
            'view-wc-summary','view-pe-summary','view-mr-summary',
            'export-wc-summary','export-pe-summary', 'export-mr-summary','export-pe','export-mr','export-all','export-deleted','export-remuneration-summary','export-master','export-scheme',
            'generate-remuneration', 'generate-engagement-card','delete-engagement-card','update-document','view-trashed-employee',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'Manager' => ['request-edit', 'request-delete', 'request-transfer','request-document-edit',
                'view-employee', 'view-allemployee','view-pe-employee','view-mr-employee','view-scheme-employee','view-deleted-employee',
                'view-remuneration','download-engagement-card','view-pe-summary','view-mr-summary',
                'export-pe-summary', 'export-mr-summary','export-pe','export-mr','export-all'],


            'Admin' => ['approve-edit', 'approve-delete', 'approve-transfer','approve-document-edit',
                'transfer-employee','delete-transfer',
                'view-allemployee','view-wc-employee','view-pe-employee','view-mr-employee','view-scheme-employee','view-deleted-employee',
                'view-employee','create-employee','delete-employee','edit-employee','edit-delete','delete-document',
                'view-anyuser','view-user','create-user','delete-user','edit-user',
                'view-anyoffice','view-office','create-office','delete-office','edit-office',
                'view-anyscheme','view-scheme','create-scheme','delete-scheme','edit-scheme',
                'view-anyrole','edit-role',
                'view-any-document-type','view-document-type','create-document-type','delete-document-type','edit-document-type',
                'import-employee', 'export-employee',
                'view-remuneration','create-remuneration', 'edit-remuneration','delete-remuneration','bulk-update-remuneration',
                'view-engagement-card','store-engagement-card','download-engagement-card',
                'view-wc-summary','view-pe-summary','view-mr-summary',
                'export-wc-summary', 'export-pe-summary', 'export-mr-summary','export-pe','export-mr','export-all','export-deleted','export-remuneration-summary','export-master','export-scheme',
                'generate-remuneration', 'generate-engagement-card','delete-engagement-card','update-document','view-trashed-employee','view-master-employee',
                ],

            'Reviewer'=>[
                'view-employee','view-allemployee','view-pe-employee','view-mr-employee','view-scheme-employee','view-deleted-employee',
                'view-remuneration','export-pe-summary', 'export-mr-summary','export-pe','export-mr','export-all',
            ],

            'Viewer'=>[
                'view-employee','view-allemployee','view-pe-employee','view-mr-employee','view-scheme-employee','view-deleted-employee',
                'view-remuneration','download-engagement-card'
            ]


        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
