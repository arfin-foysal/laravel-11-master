<?php

namespace Database\Seeders;

use App\Models\CustomRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Permission::create(['name' => 'role-and-permission-management']);
        Permission::create(['name' => 'login']);
        Permission::create(['name' => 'register']);
        Permission::create(['name' => 'create']);
        Permission::create(['name' => 'read']);
        Permission::create(['name' => 'update']);
        Permission::create(['name' => 'delete']);

        $systemAdmin = CustomRole::create(['name' => 'system-admin']);
        $superAdmin = CustomRole::create(['name' => 'super-admin']);
        $admin = CustomRole::create(['name' => 'admin']);
        $user = CustomRole::create(['name' => 'user']);

        $systemAdmin->givePermissionTo('login');
        $systemAdmin->givePermissionTo('register');
        $systemAdmin->givePermissionTo('create');
        $systemAdmin->givePermissionTo('read');
        $systemAdmin->givePermissionTo('update');
        $systemAdmin->givePermissionTo('delete');

    }
}
