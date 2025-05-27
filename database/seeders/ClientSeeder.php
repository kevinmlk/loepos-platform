<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create clients for main organization
        Client::factory(1)->create([
            'organization_id' => 1
        ]);

        Client::factory()->create([
            'organization_id' => 1,
            'first_name' => 'Jonathan',
            'last_name' => 'Schuermans',
            'email' => 'jonathan.schuermans@gmail.be',
            'phone' => '0457884936',
            'address' => 'Stationstraat 57',
            'city' => 'Jabbeke',
            'postal_code' => '8490',
            'country' => 'België',
            'national_registry_number' => '624108359'
        ]);

        Client::factory(18)->create([
            'organization_id' => 1
        ]);
    }
}
