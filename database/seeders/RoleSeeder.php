<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'superadmin']);

        // Define resources and actions for granular permissions
        $resources = [
            'users',
            'products',
            'categories',
            'subcategories',
            'units',
            'suppliers',
            'customers',
            'warehouses',
            'stores',
            'purchases',
            'sales',
            'transactions',
            'stock-batches',
            'stock-cards',
            'stock-reports',
            'profit-margin',
            'roles',
            'permissions',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        $allPermissions = [];
        foreach ($resources as $res) {
            foreach ($actions as $act) {
                $perm = "{$res}.{$act}";
                Permission::firstOrCreate(['name' => $perm]);
                $allPermissions[] = $perm;
            }
        }

        // Additional utility permissions
        Permission::firstOrCreate(['name' => 'transactions.manage']);
        $allPermissions[] = 'transactions.manage';

        // Assign all permissions to admin role by default
        if ($adminRole) {
            $adminRole->givePermissionTo($allPermissions);
        }
    }
}
