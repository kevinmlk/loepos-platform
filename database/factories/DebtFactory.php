<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Debt;
use App\Models\Dossier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dossier_id' => Dossier::factory(),
            'creditor' => fake()->company(),
            'amount' => fake()->numberBetween(0, 9999),
            'amount_paid' => fake()->numberBetween(0, 6999),
            'status' => fake()->randomElement(Debt::STATUS),
            'due_date' => fake()->date(),
        ];
    }
}
