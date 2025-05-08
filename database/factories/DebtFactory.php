<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Debt;

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
            'dossier_id' => '1',
            'creditor' => fake()->company(),
            'amount' => fake()->randomNumber(4),
            'status' => fake()->randomElement(Debt::STATUS),
            'due_date' => fake()->date(),
        ];
    }
}
