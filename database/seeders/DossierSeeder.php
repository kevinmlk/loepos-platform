<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dossier;
use App\Models\Client;

class DossierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create dossiers and attach clients
        $clients = Client::take(5)->get();
        
        foreach ($clients as $index => $client) {
            $dossier = Dossier::factory()->create([
                'user_id' => 1,
                'status' => Dossier::STATUS_ACTIVE
            ]);
            
            // Attach the client to the dossier
            $dossier->clients()->attach($client->id);
            
            // For the first dossier, attach multiple clients as an example
            if ($index === 0) {
                $dossier->clients()->attach($clients->where('id', '>', $client->id)->take(2)->pluck('id'));
            }
        }
    }
}