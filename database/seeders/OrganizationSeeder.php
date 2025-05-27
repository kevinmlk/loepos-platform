<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a main organization for testing
        Organization::factory()->create([
            'id' => 1,
            'name' => 'Main Organization',
            'email' => 'info@mainorg.com',
        ]);
        
        // Create additional organizations
        Organization::factory(9)->create();
    }
}
