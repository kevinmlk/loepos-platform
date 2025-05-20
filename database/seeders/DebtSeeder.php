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
            'amount_paid' => 2456,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 2,
            'creditor' => 'RegSol',
            'amount' => 8956,
            'amount_paid' => 4000,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 3,
            'creditor' => 'TCM',
            'amount' => 1400,
            'amount_paid' => 150,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 4,
            'creditor' => 'Coeo Incasso',
            'amount' => 4889,
            'amount_paid' => 74.99,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory()->create([
            'dossier_id' => 5,
            'creditor' => 'Justitie België',
            'amount' => 2648,
            'amount_paid' => 2406.88,
            'status' => Debt::STATUS_OPEN,
            'due_date' => now()->addYear(),
        ]);

        Debt::factory(20)->create();
    }
}
