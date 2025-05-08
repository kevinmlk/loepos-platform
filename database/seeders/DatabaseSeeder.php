<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // OrganizationSeeder::class,
            // UserSeeder::class,
            // ClientSeeder::class,
            DossierSeeder::class
        ]);

        User::factory()->create([
            'first_name' => 'James',
            'last_name' => 'Doe',
            'email' => 'james.doe@mail.be',
            'password' => 'james123',
            'organization_id' => 1,
            'role' => User::ROLE_EMPLOYEE,
        ]);
    }
}
