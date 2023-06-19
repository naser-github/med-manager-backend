<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dashboard Permission
        $dashboard_permissions = [
            ['name' => 'dashboard_read', 'guard_name' => 'web'],
        ];
        Permission::insert($dashboard_permissions);

        // Medicine Permissions
        $medicine_permissions = [
            ['name' => 'medicine_read', 'guard_name' => 'web'],
            ['name' => 'medicine_write', 'guard_name' => 'web'],
        ];
        Permission::insert($medicine_permissions);

        // Medicine Permissions
        $prescription_permissions = [
            ['name' => 'prescription_read', 'guard_name' => 'web'],
            ['name' => 'prescription_write', 'guard_name' => 'web'],
        ];
        Permission::insert($prescription_permissions);

        // Role Permissions
        $role_management_permissions = [
            ['name' => 'role_management_read', 'guard_name' => 'web'],
            ['name' => 'role_management_write', 'guard_name' => 'web'],
        ];
        Permission::insert($role_management_permissions);

        // User Permissions
        $user_management_permissions = [
            ['name' => 'user_management_read', 'guard_name' => 'web'],
            ['name' => 'user_management_write', 'guard_name' => 'web'],
        ];
        Permission::insert($user_management_permissions);
    }
}
