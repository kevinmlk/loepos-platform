<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create organizations first
        $this->call([
            OrganizationSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            DossierSeeder::class,
            DebtSeeder::class,
            FinancialInfoSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
