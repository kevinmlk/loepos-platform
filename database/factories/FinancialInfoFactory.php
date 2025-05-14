<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FinancialInfo;
use App\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialInfo>
 */
class FinancialInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iban' => 'BE' . fake()->numerify(str_repeat('#', 14)),
            'bank_name' => fake()->company(),
            'monthly_income' => fake()->numberBetween(0, 2100),
            'monthly_expenses' => fake()->numberBetween(0, 1800),
            'employer' => fake()->company(),
            'contract' => fake()->randomElement(FinancialInfo::CONTRACTS),
            'education' => fake()->randomElement(FinancialInfo::EDUCATIONS)
        ];
    }
}
