<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialInfo;

class FinancialInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialInfo::factory()->create([
            'client_id' => 1,
            'iban' => 'BE32456785991124',
            'bank_name' => 'Argenta',
            'monthly_income' => 1655.00,
            'employer' => 'Delhaize DE LEEUW',
            'contract' => FinancialInfo::CONTRACT_TEMPORARY,
            'education' => FinancialInfo::EDUCATION_SECONDARY,
        ]);

        FinancialInfo::factory()->create([
            'client_id' => 2,
            'iban' => 'BE12488698771233',
            'bank_name' => 'ING',
            'monthly_income' => 1890.50,
            'employer' => 'Karel De Grote',
            'contract' => FinancialInfo::CONTRACT_PERMANENT,
            'education' => FinancialInfo::EDUCATION_HIGHER,
        ]);

        FinancialInfo::factory()->create([
            'client_id' => 3,
            'iban' => 'BE11457136489512',
            'bank_name' => 'Argenta',
            'monthly_income' => 2060.45,
            'employer' => 'Mechelen Accountancy',
            'contract' => FinancialInfo::CONTRACT_TEMPORARY,
            'education' => FinancialInfo::EDUCATION_HIGHER,
        ]);

        FinancialInfo::factory()->create([
            'client_id' => 4,
            'iban' => 'BE58664423748799',
            'bank_name' => 'KBC',
            'monthly_income' => 2140.55,
            'employer' => 'Tech Create',
            'contract' => FinancialInfo::CONTRACT_PERMANENT,
            'education' => FinancialInfo::EDUCATION_HIGHER,
        ]);

        FinancialInfo::factory()->create([
            'client_id' => 5,
            'iban' => 'BE55324466984753',
            'bank_name' => 'KBC',
            'monthly_income' => 1499,
            'contract' => FinancialInfo::CONTRACT_UNEMPLOYED,
            'education' => FinancialInfo::EDUCATION_SECONDARY,
        ]);
    }
}
