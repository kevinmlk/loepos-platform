<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dossier;

class DossierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dossier::factory()->create([
            'client_id' => 1,
            'user_id' => 1,
            'status' => Dossier::STATUS_ACTIVE
        ]);

        Dossier::factory()->create([
            'client_id' => 2,
            'user_id' => 1,
            'status' => Dossier::STATUS_ACTIVE
        ]);

        Dossier::factory()->create([
            'client_id' => 3,
            'user_id' => 1,
            'status' => Dossier::STATUS_ACTIVE
        ]);

        Dossier::factory()->create([
            'client_id' => 4,
            'user_id' => 1,
            'status' => Dossier::STATUS_ACTIVE
        ]);

        Dossier::factory()->create([
            'client_id' => 5,
            'user_id' => 1,
            'status' => Dossier::STATUS_ACTIVE
        ]);
    }
}
