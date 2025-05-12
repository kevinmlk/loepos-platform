<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;
use App\Models\FinancialInfo;

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
            'client_id' => 1,
            'iban' => 'BE' . fake()->numerify(str_repeat('#', 14)),
            'bank_name' => fake()->company(),
            'monthly_income' => fake()->numberBetween(0, 2100),
            'employer' => fake()->company(),
            'contract' => fake()->randomElement(FinancialInfo::CONTRACTS),
            'education' => fake()->randomElement(FinancialInfo::EDUCATIONS)
        ];
    }
}
