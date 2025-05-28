<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test employee user for login
        User::factory()->create([
            'first_name' => 'James',
            'last_name' => 'Doe',
            'email' => 'james.doe@mail.be',
            'password' => 'james123',
            'organization_id' => 1,
            'role' => User::ROLE_EMPLOYEE,
        ]);

        // test admin user for login
        User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@mail.be',
            'password' => 'jane123',
            'organization_id' => 1,
            'role' => User::ROLE_ADMIN,
        ]);

        // test superadmin users for login
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Loepos',
            'email' => 'admin@loepos.be',
            'password' => 'admin123',
            'organization_id' => null,
            'role' => User::ROLE_SUPERADMIN,
        ]);

        User::factory()->create([
            'first_name' => 'Admin 2',
            'last_name' => 'Loepos',
            'email' => 'admin2@loepos.be',
            'password' => 'admin123',
            'organization_id' => 1,
            'role' => User::ROLE_SUPERADMIN,
        ]);
    }
}
