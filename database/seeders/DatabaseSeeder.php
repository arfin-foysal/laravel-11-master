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

        $user = User::factory()->create([
            'name' => 'system-admin',
            'email' => 'systemadmin@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->call(RoleAndPermissionSeeder::class);

        $user->assignRole('system-admin');
        $user->givePermissionTo('role-and-permission-management');
    }
}
