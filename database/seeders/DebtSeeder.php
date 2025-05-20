<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Debt;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Debt::factory()->create([
            'dossier_id' => 1,
            'creditor' => 'FOD',
            'amount' => 5000,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 2,
            'creditor' => 'RegSol',
            'amount' => 8956,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 3,
            'creditor' => 'TCM',
            'amount' => 1400,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 4,
            'creditor' => 'Coeo Incasso',
            'amount' => 4889,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 5,
            'creditor' => 'Justitie België',
            'amount' => 2648,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory(20)->create();
    }
}
