<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        // Create user to login with
        User::factory()->create([
            'first_name' => 'James',
            'last_name' => 'Doe',
            'email' => 'james.doe@mail.be',
            'password' => 'james123',
            'organization_id' => Organization::factory(),
        ]);
    }
}
