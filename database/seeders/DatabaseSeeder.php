<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $systemAdmin = User::factory()->create([
            'name' => 'system-admin',
            'email' => 'systemadmin@example.com',
            'username' => 'system-admin',
            'password' => bcrypt('password'),
        ]);

        $superAdmin = User::factory()->create(
            [
                'name' => 'super-admin',
                'email' => 'superadmin@example.com',
                'username' => 'super-admin',
                'password' => bcrypt('password'),
            ],
        );

        $admin = User::factory()->create(
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'organization_id' => 1,
                'password' => bcrypt('password'),
            ]
        );

        $this->call(RoleAndPermissionSeeder::class);

        $systemAdmin->assignRole('system-admin');
        $superAdmin->assignRole('super-admin');
        $admin->assignRole('admin');

        $systemAdmin->givePermissionTo('role-and-permission-management');
    }
}
